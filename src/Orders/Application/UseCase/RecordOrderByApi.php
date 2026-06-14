<?php

namespace App\Orders\Application\UseCase;

use App\Orders\Application\DTO\OrderDTO;
use App\Orders\Domain\Repository\OrderRepositoryInterface;
use App\Orders\Application\Mapper\OrderMapper;
use App\Orders\Application\Mapper\OrderItemMapper;
use App\Orders\Domain\Repository\OrderItemRepositoryInterface;
use App\Payments\Application\UseCase\CreateStripeSession;

final class RecordOrderByApi
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private OrderMapper $orderMapper,
        private OrderItemMapper $orderItemMapper,
        private OrderItemRepositoryInterface $orderItemRepository,
        private CreateStripeSession $createStripeSession
    ) {}

    public function execute(OrderDTO $orderDTO): void
    {
        // 1) Créer l'Order global
        $order = $this->orderMapper->toEntity($orderDTO);

        // 2) Regrouper les items par auteur
        $itemsByAuthor = [];

        foreach ($orderDTO->order_items as $itemDTO) {
            $authorId = $itemDTO->author_id;

            if (!isset($itemsByAuthor[$authorId])) {
                $itemsByAuthor[$authorId] = [];
            }

            $itemsByAuthor[$authorId][] = $itemDTO;
        }

        // 3) Pour chaque auteur → créer une session Stripe
        foreach ($itemsByAuthor as $authorId => $itemsDTO) {

            // Construire le panier Stripe pour cet auteur
            $cart = [
                'cart' => [
                    [
                        'items' => array_map(fn($itemDTO) => [
                            'id' => $itemDTO->book_id,
                            'quantity' => $itemDTO->quantity,
                        ], $itemsDTO)
                    ]
                ]
            ];

            // Appel Stripe → 1 PaymentIntent par auteur
            $session = $this->createStripeSession->execute($cart);

            // 4) Mapper les OrderItems et stocker les IDs Stripe
            foreach ($itemsDTO as $itemDTO) {
                $orderItem = $this->orderItemMapper->toEntity($itemDTO);

                $orderItem->setStripeSessionId($session['stripe_session_id']);
                $orderItem->setStripePaymentIntentId($session['stripe_payment_intent_id']);

                $order->addOrderItem($orderItem);
            }
        }

        // 5) Calcul du total global
        $total = 0;
        foreach ($order->getOrderItems() as $item) {
            $total += $item->getTotalPrice();
        }

        $order->setTotalAmount($total);

        // 6) Sauvegarde finale
        $this->orderRepository->save($order);
    }
}
