<?php

namespace App\SellerPayments\Application\UseCase;

use App\SellerPayments\Domain\Repository\SellerPaymentRepositoryInterface;
use App\Enum\SellerPaymentStatus;
use App\Enum\OrderItemStatus;
use App\Orders\Domain\Service\OrderNotificationMailerInterface;
use App\Payments\Application\UseCase\FinalizeOrderPayment;

final class AcceptSellerPayment
{
    public function __construct(
        private SellerPaymentRepositoryInterface $repository,
        private OrderNotificationMailerInterface $notificationMailer,
        private FinalizeOrderPayment $finalizeOrderPayment,
    ) {
    }

    public function execute(
        int $id,
        string $confirmationToken
    ): void {

        /*
         * Charger le SellerPayment
         */
        $SP = $this->repository->find($id);

        if (!$SP) {
            throw new \RuntimeException(
                'Commande introuvable'
            );
        }

        /*
         * Vérifier le token
         */
        if (
            $SP->getConfirmationToken() !==
            $confirmationToken
        ) {
            throw new \RuntimeException(
                'Token invalide'
            );
        }

        /*
         * Vérifier l'expiration
         */
        if (
            $SP->getConfirmationTokenExpiresAt() <
            new \DateTimeImmutable(
                'now',
                new \DateTimeZone('Europe/Paris')
            )
        ) {
            throw new \RuntimeException(
                'Lien expiré'
            );
        }

        /*
         * Vérifier le statut
         */
        if (
            $SP->getStatus() !==
            SellerPaymentStatus::WAITING_SELLER_CONFIRMATION
        ) {
            throw new \RuntimeException(
                'Commande déjà traitée'
            );
        }

        /*
         * Confirmer les OrderItems
         */
        foreach ($SP->getOrderItems() as $orderItem) {

            $orderItem->setStatus(
                OrderItemStatus::CONFIRMED
            );

            $orderItem->setUpdatedAt(
                new \DateTimeImmutable(
                    'now',
                    new \DateTimeZone('Europe/Paris')
                )
            );

            $book = $orderItem->getBook();

            if (!$book) {
                throw new \RuntimeException(
                    'Livre introuvable dans le OrderItem'
                );
            }

            $book->confirmReservation(
                $orderItem->getQuantity()
            );
        }

        /*
         * Confirmer le SellerPayment
         */
        $SP->setStatus(
            SellerPaymentStatus::CONFIRMED
        );

        $SP->setSellerConfirmedAt(
            new \DateTimeImmutable(
                'now',
                new \DateTimeZone('Europe/Paris')
            )
        );

        $SP->setUpdatedAt(
            new \DateTimeImmutable(
                'now',
                new \DateTimeZone('Europe/Paris')
            )
        );

        /*
         * Détruire le token
         */
        $SP->setConfirmationToken(null);
        $SP->setConfirmationTokenExpiresAt(null);

        /*
         * Mettre à jour la commande
         */
        $SP->getOrder()->setUpdatedAt(
            new \DateTimeImmutable(
                'now',
                new \DateTimeZone('Europe/Paris')
            )
        );

        /*
         * Sauvegarder avant la finalisation.
         *
         * Important :
         * hasPendingSellerPayments() doit voir ce SellerPayment
         * comme CONFIRMED.
         */
        $this->repository->save($SP);

        /*
         * Finaliser éventuellement la commande.
         *
         * Si d'autres vendeurs attendent encore :
         * -> FinalizeOrderPayment ne fait rien.
         *
         * Si tout le monde a répondu :
         * -> capture + PaySeller.
         */
        $this->finalizeOrderPayment->execute($SP);

        /*
         * Vérification du stock
         */
        foreach ($SP->getOrderItems() as $orderItem) {

            $book = $orderItem->getBook();

            if (
                $book &&
                $book->getQuantity() === 0
            ) {
                $this->notificationMailer
                    ->sendOutOfStockMail($orderItem);
            }
        }
    }
}