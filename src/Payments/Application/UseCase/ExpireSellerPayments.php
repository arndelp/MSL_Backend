<?php

namespace App\Payments\Application\UseCase;

use App\Enum\CancellationReason;
use App\Enum\OrderItemStatus;
use App\Enum\SellerPaymentStatus;
use App\SellerPayments\Domain\Repository\SellerPaymentRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Payments\Application\UseCase\FinalizeOrderPayment;

final class ExpireSellerPayments
{
    public function __construct(
        private SellerPaymentRepositoryInterface $repository,
        private FinalizeOrderPayment $finalizeOrderPayment,
        private EntityManagerInterface $entityManager,
        
    ) {
    }

    public function execute(): void
    {
        $now = new \DateTimeImmutable(
            'now',
            new \DateTimeZone('Europe/Paris')
        );

        $sellerPayments =
            $this->repository->findExpiredSellerPayments($now);

        foreach ($sellerPayments as $sellerPayment) {

            /*
             * Double sécurité.
             */
            if (
                $sellerPayment->getStatus() !==
                SellerPaymentStatus::WAITING_SELLER_CONFIRMATION
            ) {
                continue;
            }

            /*
             * Annulation du SellerPayment.
             */
            $sellerPayment->setStatus(
                SellerPaymentStatus::CANCELLED
            );

            $sellerPayment->setCancellationReason(
                CancellationReason::SELLER_TIMEOUT
            );

            $sellerPayment->setCancelledAt($now);

            $sellerPayment->setConfirmationToken(null);
            $sellerPayment->setConfirmationTokenExpiresAt(null);

            $sellerPayment->setUpdatedAt($now);

            /*
             * Annulation des OrderItems
             * et libération du stock.
             */
            foreach ($sellerPayment->getOrderItems() as $orderItem) {

                $orderItem->setStatus(
                    OrderItemStatus::CANCELLED
                );

                $orderItem->setUpdatedAt($now);

                $book = $orderItem->getBook();

                if (!$book) {
                    throw new \RuntimeException(
                        'Livre introuvable dans la commande.'
                    );
                }

                $book->cancelReservation(
                    $orderItem->getQuantity()
                );
            }

            /*
             * Sauvegarde avant de finaliser.
             *
             * Ainsi hasPendingSellerPayments()
             * verra bien ce SellerPayment comme CANCELLED.
             */
            $this->entityManager->flush();

            /*
             * Vérifie si la commande peut maintenant
             * être finalisée.
             *
             * - s'il reste des vendeurs en attente → rien
             * - sinon → capture ou annulation
             */
            $this->finalizeOrderPayment->execute(
                $sellerPayment
            );
        }
    }
}