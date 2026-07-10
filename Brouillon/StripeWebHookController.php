<?php

namespace App\Payments\UI\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Stripe\Webhook;
use App\Orders\Domain\Repository\OrderItemRepositoryInterface;
use App\Orders\Domain\Repository\OrderRepositoryInterface;

class StripeWebhookController
{
    public function __construct(
        private OrderItemRepositoryInterface $orderItemRepository,
        private OrderRepositoryInterface $orderRepository,
        private string $stripeWebhookSecret
    ) {}

    public function __invoke(Request $request): Response
    {
        $payload = $request->getContent();
        $sigHeader = $request->headers->get('stripe-signature');

        try {
            $event = Webhook::constructEvent(
                $payload,
                $sigHeader,
                $this->stripeWebhookSecret
            );
        } catch (\Exception $e) {
            return new Response('Signature invalide', 400);
        }

        switch ($event->type) {

            // 🔵 Le client a payé → PaymentIntent créé et autorisé
            case 'checkout.session.completed':
                $session = $event->data->object;
                // Optionnel : utile si tu veux marquer l’item comme "paid_pending_validation"
                break;

            // 🟢 Paiement capturé (après validation vendeur)
            case 'payment_intent.succeeded':
                $intent = $event->data->object;
                $this->handlePaymentIntentSucceeded($intent->id);
                break;

            // 🔴 Paiement annulé (rejet vendeur)
            case 'payment_intent.canceled':
                $intent = $event->data->object;
                $this->handlePaymentIntentCanceled($intent->id);
                break;

            // ⚠️ Paiement échoué
            case 'payment_intent.payment_failed':
                $intent = $event->data->object;
                $this->handlePaymentIntentFailed($intent->id);
                break;
        }

        return new Response('OK', 200);
    }

    private function handlePaymentIntentSucceeded(string $paymentIntentId): void
    {
        $item = $this->orderItemRepository->findByStripePaymentIntentId($paymentIntentId);

        if (!$item) return;

        $item->setStatus('validated');
        $this->orderItemRepository->save($item);

        $this->updateOrderStatus($item->getOrder());
    }

    private function handlePaymentIntentCanceled(string $paymentIntentId): void
    {
        $item = $this->orderItemRepository->findByStripePaymentIntentId($paymentIntentId);

        if (!$item) return;

        $item->setStatus('rejected');
        $this->orderItemRepository->save($item);

        $order = $item->getOrder();
        $order->setStatus('rejected');
        $this->orderRepository->save($order);
    }

    private function handlePaymentIntentFailed(string $paymentIntentId): void
    {
        $item = $this->orderItemRepository->findByStripePaymentIntentId($paymentIntentId);

        if (!$item) return;

        $item->setStatus('failed');
        $this->orderItemRepository->save($item);

        $order = $item->getOrder();
        $order->setStatus('failed');
        $this->orderRepository->save($order);
    }

    private function updateOrderStatus($order): void
    {
        $allValidated = true;

        foreach ($order->getOrderItems() as $item) {
            if ($item->getStatus() !== 'validated') {
                $allValidated = false;
                break;
            }
        }

        if ($allValidated) {
            $order->setStatus('validated');
            $this->orderRepository->save($order);
        }
    }
}
