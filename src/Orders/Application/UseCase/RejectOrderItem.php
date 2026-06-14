<?php

namespace App\Orders\Application\UseCase;

use App\Orders\Domain\Repository\OrderItemRepositoryInterface;
use App\Orders\Domain\Repository\OrderRepositoryInterface;
use App\Payments\Application\UseCase\CancelStripePayment;

final class RejectOrderItem
{
    public function __construct(
        private OrderItemRepositoryInterface $orderItemRepository,
        private OrderRepositoryInterface $orderRepository,
        private CancelStripePayment $cancelStripePayment
    ) {}

    public function execute(int $orderItemId): void
    {
        // 1) Récupération de l'OrderItem
        $item = $this->orderItemRepository->find($orderItemId);

        if (!$item) {
            throw new \RuntimeException("OrderItem introuvable");
        }

        // 2) Annulation du PaymentIntent associé
        $paymentIntentId = $item->getStripePaymentIntentId();

        if ($paymentIntentId) {
            $this->cancelStripePayment->execute($paymentIntentId);
        }

        // 3) Mise à jour du statut de l'item
        $item->setStatus('cancelled');
        $this->orderItemRepository->save($item);

        // 4) Mise à jour du statut global de la commande
        $order = $item->getOrder();

        // Si un seul item est rejeté → commande rejetée
        $order->setStatus('partially_cancelled');
        $this->orderRepository->save($order);
    }
}
