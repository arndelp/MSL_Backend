<?php

namespace App\Orders\Application\UseCase;


use App\Orders\Domain\Repository\OrderItemRepositoryInterface;
use App\Payments\Domain\Payment\StripeGatewayInterface;
use App\Enum\OrderItemStatus;
use App\Orders\Application\DTO\OrderItemCancellationDTO;
use App\Enum\CancellationReason;
use App\Enum\PayoutStatus;

final class RefuseOrderItem
{
    public function __construct(
        private OrderItemRepositoryInterface $repository,
        private StripeGatewayInterface $stripeGateway,
    ) {}

    public function execute(int $id,  OrderItemCancellationDTO $dto): void
    {
        //charger l'orderItem
        $orderItem = $this->repository->find($id);

        if (!$orderItem) {
            throw new \RuntimeException('Commande introuvable');
        }

        //Vérifier le token
        if ($orderItem->getConfirmationToken() !== $dto->confirmationToken) {
            throw new \RuntimeException('Token invalide');
        }

        //Vérifier l'expiration
        if (
            $orderItem->getConfirmationTokenExpiresAt() <
            new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris'))
        ) {
            throw new \RuntimeException('Lien expiré');
        }

        //Vérifier le statut
        if (
            $orderItem->getStatus() !==
            OrderItemStatus::PENDING_AUTHOR_CONFIRMATION
        ) {
            throw new \RuntimeException('Commande déjà traitée');
        }

        //Annuler le paiement (méthode du StripeGateway)
        $this->stripeGateway->cancelPaymentIntent(
            $orderItem->getStripePaymentIntentId()
        );

        //Modifier la commande
        $orderItem->setStatus(
            OrderItemStatus::CANCELLED
        );
        //Raison de l'annulation
        //conversion en ENUM-> Pas de mapper , pour simplifier étant donné qu'il n'y a qu'un champs à traiter.
        $orderItem->setCancellationReason(
            CancellationReason::from($dto->reason)
        );

        $orderItem->setPayoutStatus(
            PayoutStatus::CANCELLED
        );

         $orderItem->setCancelledAt(
            new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris'))
        );

        $orderItem->setUpdatedAt(
             new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris'))
        );

        //Détruire le token
        $orderItem->setConfirmationToken(null);

        $orderItem->setConfirmationTokenExpiresAt(null);

        //Sauvegarder
        $this->repository->save($orderItem);
    }
}

