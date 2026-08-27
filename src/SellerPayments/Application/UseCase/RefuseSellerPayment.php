<?php

namespace App\SellerPayments\Application\UseCase;

use App\SellerPayments\Domain\Repository\SellerPaymentRepositoryInterface;
use App\Enum\OrderItemStatus;
use App\Enum\SellerPaymentStatus;
use App\SellerPayments\Application\DTO\SellerPaymentCancellationDTO;
use App\Enum\CancellationReason;
use App\Payments\Application\UseCase\FinalizeOrderPayment;

final class RefuseSellerPayment
{
    public function __construct(
        private SellerPaymentRepositoryInterface $repository,
        private FinalizeOrderPayment $finalizeOrderPayment,
    ) {
    }

    public function execute(
        int $id,
        SellerPaymentCancellationDTO $dto
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
            $dto->confirmationToken
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
         * Annuler les OrderItems
         */
        foreach ($SP->getOrderItems() as $orderItem) {

            $orderItem->setStatus(
                OrderItemStatus::CANCELLED
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
                    'Livre introuvable dans la commande'
                );
            }

            /*
             * Libérer la réservation
             */
            $book->cancelReservation(
                $orderItem->getQuantity()
            );
        }

        /*
         * Annuler le SellerPayment
         */
        $SP->setStatus(
            SellerPaymentStatus::CANCELLED
        );

        $SP->setCancellationReason(
            CancellationReason::from($dto->reason)
        );

        $SP->setCancelledAt(
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
         * Sauvegarder avant la finalisation.
         */
        $this->repository->save($SP);

        /*
         * Finaliser éventuellement la commande.
         *
         * Exemple :
         *
         * Vendeur A -> CONFIRMED
         * Vendeur B -> CANCELLED
         *
         * Aucun WAITING :
         * -> capture du vendeur A
         * -> PaySeller(A)
         */
        $this->finalizeOrderPayment->execute($SP);
    }
}