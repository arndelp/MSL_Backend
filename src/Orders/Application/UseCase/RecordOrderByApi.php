<?php

namespace App\Orders\Application\UseCase;

use App\Orders\Application\DTO\OrderDTO;
use App\Orders\Application\Mapper\OrderItemMapper;
use App\Orders\Application\Mapper\OrderMapper;
use App\Orders\Domain\Repository\OrderRepositoryInterface;
use App\Orders\Domain\Repository\OrderItemRepositoryInterface;
use App\Payments\Application\UseCase\CreateStripeSession;
use Symfony\Bundle\SecurityBundle\Security;
use App\SellerPayments\Domain\Entity\SellerPayment;
use App\Enum\SellerPaymentStatus;
use App\Enum\ShippingStatus;
use App\Enum\OrderItemStatus;
use App\Enum\OrderStatus;


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

    //génération du numéro de commande
    private function generateOrderNumber(): string
    {
        return 'MSL-' .
            (new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris')))->format('YmdHis') .
            '-' .
            strtoupper(bin2hex(random_bytes(3)));
    }


    public function execute(OrderDTO $orderDTO): array
    {
        /*
        * 1) Récupération de l'acheteur connecté
        */
        $buyer = $this->security->getUser();

        if (!$buyer) {
            throw new \Exception('Utilisateur non authentifié');
        }

        /*
        * 2) Création de la commande globale
        */
        $order = $this->orderMapper->toEntity($orderDTO);

        //Création du numéro de commande
        $order->setOrderNumber($this->generateOrderNumber()); // Gén

        // utilisateur qui achète
        $order->setUserId($buyer);

        $order->setStatus(OrderStatus::PENDING_PAYMENT);

        /*
        * 3) Création des OrderItems
        */
        foreach ($orderDTO->order_items as $itemDTO) {

            $orderItem = $this->orderItemMapper->toEntity($itemDTO);
               // Le vendeur est récupéré par le le livre
            $book = $orderItem->getBook();

            if (!$book) {
                throw new \Exception(
                    'Livre introuvable pour la commande'
                );
            }

            // Vendeur
            $seller = $book->getUser();

            if (!$seller) {
                throw new \Exception(
                    'Le livre n\'a pas de vendeur'
                );
            }
            
            $orderItem->setSeller($seller);

            $orderItem->setCreatedAt(new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris')));

            $orderItem->setStatus(
                OrderItemStatus::CREATED
            );

            

             // Ajout des orderItems dans Order             
            $order->addOrderItem($orderItem);
            //Maintenant que les OrderItems sont regroupés dans orders, on pourra les dégrouper par Vendeur
            
        }

        /*
        * 4) Regroupement des OrderItems par vendeur
        */
        
        $itemsBySeller = [];
        // Pour chaque orderItem de la commande, on récupère le vendeur associé
        foreach ($order->getOrderItems() as $orderItem) {

            $seller = $orderItem->getSeller();

            if (!$seller) {
                throw new \Exception(
                    'OrderItem sans vendeur'
                );
            }
            // Récupération de l'ID du vendeur
            $sellerId = $seller->getId();
            // Vérification si le vendeur est déjà présent dans le tableau
            if (!isset($itemsBySeller[$sellerId])) {
                $itemsBySeller[$sellerId] = [];
            }
            // Ajout des OrderItems au tableau regroupé
            $itemsBySeller[$sellerId][] = $orderItem;
        }
        
        /*
        * 5) Création d'un SellerPayment par vendeur
        */

        // Création d'un index pour les payments par vendeur
        $sellerPaymentIndex = 1;

        foreach ($itemsBySeller as $sellerItems) {
            // Récupération du vendeur associé. [0]: On prend le premier OrderItem pour récupérer le vendeur
            $seller = $sellerItems[0]->getSeller();

            if (!$seller) {
                throw new \Exception(
                    'Vendeur introuvable pour SellerPayment'
                );
            }

//////////////TOUS LES MONTANTS SONT EN CENTIMES///////////////////

            // Calcul du sous-total vendeur

            $subtotal = 0;

            foreach ($sellerItems as $orderItem) {

                $subtotal += (int) $orderItem->getUnitPrice() * (int) $orderItem->getQuantity();
            }
            
            // Livraison temporairement à 0.                  
            $shippingAmount = 0;

            // Total vendeur
            $totalAmount = $subtotal + $shippingAmount;


            /*
            * Commission plateforme. Pour l'instant 12 %.                          
            */
            $platformFee = (int) round($totalAmount * 0.12);


            // Montant revenant au vendeur.  (total - commission plateforme - frais de livraison)          
            $sellerAmount = $totalAmount - $platformFee - $shippingAmount;


            ///////////// Création du SellerPayment////////////////////
            $sellerPayment = new SellerPayment();

            $sellerPayment->setSeller($seller);

            //Génération du sellerPayment
            $sellerPayment->setPaymentNumber(
                $order->getOrderNumber() . '-' .
                str_pad(
                    (string) ($sellerPaymentIndex ?? 1),
                    2,
                    '0',
                    STR_PAD_LEFT
                )
            );

            $sellerPayment->setStatus(
                SellerPaymentStatus::CREATED
            );

            $sellerPayment->setSubtotalAmount(
                (string) $subtotal
            );

            $sellerPayment->setShippingAmount(
                (string) $shippingAmount
            );

            $sellerPayment->setTotalAmount(
                (string) $totalAmount
            );

            $sellerPayment->setPlatformFee(
                (string) $platformFee
            );

            $sellerPayment->setSellerAmount(
                (string) $sellerAmount
            );

            $sellerPayment->setCurrency(
                $order->getCurrency() ?? 'EUR'
            );

            $sellerPayment->setShippingStatus(
                ShippingStatus::WAITING_LABEL
            );

            $sellerPayment->setCreatedAt(
                new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris'))
            );
   
           
          

         
            

            /*
            * Ajout des OrderItems au SellerPayment
            */
            foreach ($sellerItems as $orderItem) {

                $sellerPayment->addOrderItem($orderItem);
            }

            /*
            * Ajout du SellerPayment à la commande
            */
            $order->addSellerPayment($sellerPayment);

            //Incrémentation du sellerPaymentIndex
            $sellerPaymentIndex++;


//////FIN DES AJOUTS DANS sellerPayments////////////////
        }


        /*
        * 6) Calcul du montant total de la commande
        */
        $total = 0;

        foreach ($order->getSellerPayments() as $payment) {

            $total += (int) $payment->getTotalAmount();
        }

        $order->setTotalAmount((string) $total);
        
        /*
        * 7)  Stripe : création d'une seule session CheckoutSesion pour la commande globale
        */        

        $session = $this->createStripeSession->execute($order);

        $order->setStripeSessionId(
            $session['stripe_session_id']
        );

        /*
        * 8) Sauvegarde globale
        */
        $this->orderRepository->save($order);

        /*
        * 9) Réponse
        */
        return [
            'url' => $session['url']
        ];


    }
}