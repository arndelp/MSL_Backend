<?php

namespace App\SellerPayments\Application\UseCase;


use App\SellerPayments\Domain\Repository\SellerPaymentRepositoryInterface;
use App\Payments\Domain\Payment\StripeGatewayInterface;
use App\Enum\SellerPaymentStatus;
use App\Enum\OrderItemStatus;
use App\Enum\OrderStatus;
use App\Orders\Domain\Service\OrderNotificationMailerInterface;


final class AcceptSellerPayment
{
    public function __construct(
    private SellerPaymentRepositoryInterface $repository,    
    private OrderNotificationMailerInterface $notificationMailer,
    private StripeGatewayInterface $stripeGateway,    
    ){}




    public function execute(int $id, string $confirmationToken): void
    {
        //charger le sellerPayment ($SP)
        $SP = $this->repository->find($id);

        if (!$SP) {
            throw new \RuntimeException('Commande introuvable');
        }

        //Vérifier le token
        if ($SP->getConfirmationToken() !== $confirmationToken) {
            throw new \RuntimeException('Token invalide');
        }

        //Vérifier l'expiration
        if (
            $SP->getConfirmationTokenExpiresAt() <
            new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris'))
        ) {
            throw new \RuntimeException('Lien expiré');
        }

        //Vérifier le statut
        if (
            $SP->getStatus() !==
            SellerPaymentStatus::WAITING_SELLER_CONFIRMATION
        ) {
            throw new \RuntimeException('Commande déjà traitée');
        }
      


        //Gestion du stock: quantityReserved et soustrait de quantity
        foreach ($SP->getOrderItems() as $orderItem) {

            $orderItem->setStatus(
                OrderItemStatus::CONFIRMED
            );

            $orderItem->setUpdatedAt(
                 new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris'))
            );

            $book = $orderItem->getBook();

            if (!$book) {
                throw new \Exception('Livre introuvable dans le OrderItem');
            }

            $book->confirmReservation(
                $orderItem->getQuantity()
            );
        }          
        //Modifier la commande
        $SP->setStatus(
            SellerPaymentStatus::CONFIRMED
        );

       $SP->setSellerConfirmedAt(
            new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris'))
        );

        $SP->setUpdatedAt(
            new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris'))
        );

        //Détruire le token
        $SP->setConfirmationToken(null);

        $SP->setConfirmationTokenExpiresAt(null);     
        
        $SP->getOrder()->setUpdatedAt(
            new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris'))
        );

        //Sauvegarder la première parti pour que le sellerpayment soit enregistrée CONFIRMED avant le hasPendingSellerPayment
        $this->repository->save($SP);

        //Envoi des emails

        //Email pour informer le client de l'acceptation du vendeur
        //$this->notificationMailer->sendOrderConfirmationEmailToBuyer($orderItem);


        // Maintenant vérifier
        $hasPendingSellerPayments =
            $this->repository->hasPendingSellerPayments(
                $SP->getOrder()
            );

        if (!$hasPendingSellerPayments) {

            if($SP->getOrder()->getStatus() === OrderStatus::PAID)
                {
                    throw new \RuntimeException(
                    'Le paiement de cette commande a déjà été capturé.'
        );
                }

            $amountToCapture = 0;

            foreach ($SP->getOrder()->getSellerPayments() as $sellerPayment) {

                if (
                    $sellerPayment->getStatus() ===
                    SellerPaymentStatus::CONFIRMED
                ) {
                    $amountToCapture += (int) $sellerPayment->getTotalAmount();
                }
            }

            $this->stripeGateway->capturePaymentIntent(
                $SP->getOrder()->getStripePaymentIntentId(),
                $amountToCapture
            );

            $SP->getOrder()->setPaidAt(
                new \DateTimeImmutable(
                    'now',
                    new \DateTimeZone('Europe/Paris')
                )
            );

            $SP->getOrder()->setStatus(OrderStatus::PAID);

            
        }

       //Sauvegarder (la deuxième partie de la méthode)
        $this->repository->save($SP);

        //Vérification du stock après achat; Si quantity===0 alors envoie d'un email au vendeur 
        foreach ($SP->getOrderItems() as $orderItem) {
            $book = $orderItem->getBook();
             if ($book->getQuantity() === 0) {
            $this->notificationMailer->sendOutOfStockMail($orderItem);
            }
        }
    }
}