<?php

namespace App\SellerPayments\Application\UseCase;


use App\SellerPayments\Domain\Repository\SellerPaymentRepositoryInterface;
use App\Enum\OrderItemStatus;
use App\Enum\SellerPaymentStatus;
use App\SellerPayments\Application\DTO\SellerPaymentCancellationDTO;
use App\Enum\CancellationReason;
use App\Enum\PayoutStatus;
use App\Payments\Domain\Payment\StripeGatewayInterface;
use App\Enum\OrderStatus;



final class RefuseSellerPayment
{
    public function __construct(
        private SellerPaymentRepositoryInterface $repository,
        private StripeGatewayInterface $stripeGateway,
    ) {}

    public function execute(int $id,  SellerPaymentCancellationDTO $dto): void
    {
        //charger le sellerPayment
        $SP = $this->repository->find($id);

        

        if (!$SP) {
            throw new \RuntimeException('Commande introuvable');
        }

        //Vérifier le token
        if ($SP->getConfirmationToken() !== $dto->confirmationToken) {
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

        foreach ($SP->getOrderItems() as $orderItem) {

            $orderItem->setStatus(
                OrderItemStatus::CANCELLED
            );

            $orderItem->setUpdatedAt(
                 new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris'))
            );
         //Gestion du stock   
            $book = $orderItem->getBook();

            if (!$book) {
                throw new \RuntimeException('Livre introuvable dans la commande');
            }

            $book->cancelReservation(
                $orderItem->getQuantity()
            );

        }
        $SP->setStatus(
            SellerPaymentStatus::CANCELLED
        );            
        
        //Raison de l'annulation
        //conversion en ENUM-> Pas de mapper , pour simplifier étant donné qu'il n'y a qu'un champs à traiter.
        $SP->setCancellationReason(
            CancellationReason::from($dto->reason)
        );

        $SP->setCancelledAt(
            new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris'))
        );        

        $SP->setUpdatedAt(
             new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris'))
        );

        //Détruire le token
        $SP->setConfirmationToken(null);

        $SP->setConfirmationTokenExpiresAt(null);

        //Sauvegarder la première partie de la méthode
        $this->repository->save($SP);

        //Envoie d'un mail  (à faire)

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

            if($amountToCapture === 0) {
                $this->stripeGateway->cancelPaymentIntent(
                    $SP->getOrder()->getStripePaymentIntentId()
                );

                $SP->getOrder()->setStatus(OrderStatus::CANCELLED);
            }

            if($amountToCapture > 0) {
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
           
            $this->repository->save($SP);
        }
    }
}

