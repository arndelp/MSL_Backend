<?php

namespace App\Orders\Application\UseCase;

use App\Orders\Domain\Repository\OrderItemRepositoryInterface;
use App\Orders\Domain\Repository\OrderRepositoryInterface;
use App\Payments\Application\UseCase\CaptureStripePayment;

final class ValidateOrderItem
{
    public function __construct(
        private OrderItemRepositoryInterface $orderItemRepository,
        private OrderRepositoryInterface $orderRepository,
        private CaptureStripePayment $captureStripePayment
    ) {}

    public function execute(int $orderItemId): void
    {
        // 1) Récupération de l'OrderItem
        $item = $this->orderItemRepository->find($orderItemId);

        if (!$item) {
            throw new \RuntimeException("OrderItem introuvable");
        }

        // 2) Vérification du PaymentIntent
        $paymentIntentId = $item->getStripePaymentIntentId();

        if (!$paymentIntentId) {
            throw new \RuntimeException("Aucun PaymentIntent associé à cet item");
        }

        // 3) Capture du paiement (débit réel pour cet auteur)
        $this->captureStripePayment->execute($paymentIntentId);

        // 4) Mise à jour du statut de l'item
        $item->setStatus('confirmed');
        $this->orderItemRepository->save($item);

        // 5) Vérifier si tous les items de la commande sont validés
        $order = $item->getOrder();
        $allValidated = true;

        foreach ($order->getOrderItems() as $orderItem) {
            if ($orderItem->getStatus() !== 'confirmed') {
                $allValidated = false;
                break;
            }
        }

        // 6) Si tous les items sont validés → commande validée
        if ($allValidated) {
            $order->setStatus('partially_confirmed');
            $this->orderRepository->save($order);
        }
    }
}
