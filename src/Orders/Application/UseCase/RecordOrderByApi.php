<?php

namespace App\Orders\Application\UseCase;

use App\Orders\Application\DTO\OrderDTO;
use App\Orders\Application\Mapper\OrderItemMapper;
use App\Orders\Application\Mapper\OrderMapper;
use App\Orders\Domain\Repository\OrderRepositoryInterface;
use App\Orders\Domain\Repository\OrderItemRepositoryInterface;
use App\Payments\Application\UseCase\CreateStripeSession;
use Symfony\Bundle\SecurityBundle\Security;

final class RecordOrderByApi
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private OrderMapper $orderMapper,
        private OrderItemMapper $orderItemMapper,
        private OrderItemRepositoryInterface $orderItemRepository,
        private CreateStripeSession $createStripeSession,
        private Security $security,
    ) {
    }


    public function execute(OrderDTO $orderDTO): array
    {
        /*1) Récupération de l'acheteur connecté         */
        $buyer = $this->security->getUser();

        if (!$buyer) {
            throw new \Exception('Utilisateur non authentifié');
        }


        /*2) Création de la commande globale         */
        $order = $this->orderMapper->toEntity($orderDTO);

            //utilisateur qui achète
        $order->setUserId($buyer);

        /*3) Création des OrderItems */
        foreach ($orderDTO->order_items as $itemDTO) {

            $orderItem = $this->orderItemMapper->toEntity($itemDTO);

            /*Acheteur*/
            $orderItem->setBuyerUser($buyer);

            /*Vendeur récupéré depuis le livre*/
            $book = $orderItem->getBook();

            if (!$book) {
                throw new \Exception(
                    'Livre introuvable pour la commande'
                );
            }

            $seller = $book->getUser();

            if (!$seller) {
                throw new \Exception(
                    'Le livre n\'a pas de vendeur'
                );
            }

            // vendeur
            $orderItem->setUser($seller);

            /*Ajout dans Order             */
            $order->addOrderItem($orderItem);
        }

        /*4) Création des sessions Stripe par vendeur */
        $itemsBySeller = [];


        foreach ($order->getOrderItems() as $orderItem) {

            $sellerId = $orderItem
                ->getUser()
                ->getId();

            if (!isset($itemsBySeller[$sellerId])) {
                $itemsBySeller[$sellerId] = [];
            }

            $itemsBySeller[$sellerId][] = $orderItem;
        }

        $stripeUrl = null;

        foreach ($itemsBySeller as $sellerItems) {

            $cart = [
                'cart' => array_map(
                    fn($orderItem) => [
                        'id' => $orderItem
                            ->getBook()
                            ->getId(),

                        'quantity' => $orderItem
                            ->getQuantity(),
                    ],
                    $sellerItems
                )
            ];

            $session = $this->createStripeSession
                ->execute($cart);             
         

            // Récupération du sessionId
            foreach ($sellerItems as $orderItem) {

                $orderItem->setStripeSessionId(
                    $session['stripe_session_id']
                );               
            }

            //URL Stripe
            $stripeUrl = $session['url'];
        }

        /*5) Calcul total commande  */
        $total = 0;

        foreach ($order->getOrderItems() as $item) {

            $total += $item->getTotalPrice();
        }

        $order->setTotalAmount($total);

        /*6) Sauvegarde */
        $this->orderRepository->save($order);

    return [
        'url' => $stripeUrl
    ];
    }
}