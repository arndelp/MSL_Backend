<?php

namespace App\Orders\Application\UseCase;

use App\Orders\Domain\Repository\OrderItemRepositoryInterface;
use App\Payments\Domain\Payment\StripeGatewayInterface;
use App\Enum\OrderItemStatus;
final class AcceptOrderItem
{
    public function __construct(
    private OrderItemRepositoryInterface $repository,
    private StripeGatewayInterface $stripeGateway,
){}

public function execute(int $id, string $confirmationToken): void
{
    //charger l'orderItem
    $orderItem = $this->repository->find($id);

    if (!$orderItem) {
        throw new \RuntimeException('Commande introuvable');
    }

    //Vérifier le token
    if ($orderItem->getConfirmationToken() !== $confirmationToken) {
        throw new \RuntimeException('Token invalide');
    }

    //Vérifier l'expiration
    if (
        $orderItem->getConfirmationTokenExpiresAt() <
        new \DateTimeImmutable()
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

    //Capturer le paiement (méthode du StripeGateway)
    $this->stripeGateway->capturePaymentIntent(
        $orderItem->getStripePaymentIntentId()
    );

    //Modifier la commande
    $orderItem->setStatus(
        OrderItemStatus::CONFIRMED
    );

    $orderItem->setUserConfirmedAt(
        new \DateTimeImmutable()
    );

    //Détruire le token
    $orderItem->setConfirmationToken(null);

    $orderItem->setConfirmationTokenExpiresAt(null);

    //Sauvegarder
    $this->repository->save($orderItem);

}



}