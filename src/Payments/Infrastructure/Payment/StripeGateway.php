<?php

namespace App\Payments\Infrastructure\Payment;

use Stripe\StripeClient;
use App\Payments\Domain\Payment\StripeGatewayInterface;
use App\Orders\Domain\Entity\Order;

class StripeGateway implements StripeGatewayInterface
{
    private StripeClient $stripe;

    public function __construct(
        StripeClient $stripe
    ) {
        $this->stripe = $stripe;
    }

    public function createSession(Order $order): array
    {
        $lineItems = [];

        foreach ($order->getOrderItems() as $orderItem) {

            $book = $orderItem->getBook();

            if (!$book) {
                throw new \Exception(
                    'Livre introuvable pour un OrderItem.'
                );
            }

            $quantity = $orderItem->getQuantity();

            if ($quantity <= 0) {
                throw new \Exception(
                    'La quantité du livre doit être supérieure à zéro.'
                );
            }

            $unitPrice = (int) $orderItem->getUnitPrice();

            if ($unitPrice < 0) {
                throw new \Exception(
                    'Prix du livre invalide.'
                );
            }

            $lineItems[] = [
                'price_data' => [
                    'currency' => strtolower(
                        $order->getCurrency() ?? 'eur'
                    ),
                    'product_data' => [
                        'name' => $orderItem->getBookTitle()
                            ?? $book->getTitle(),
                    ],
                    'unit_amount' => $unitPrice,
                ],
                'quantity' => $quantity,
            ];
        }

        if (empty($lineItems)) {
            throw new \Exception(
                'Impossible de créer la session Stripe : aucun article.'
            );
        }

        $session = $this->stripe->checkout->sessions->create([
            'payment_method_types' => ['card'],

            'mode' => 'payment',

            'line_items' => $lineItems,

            'payment_intent_data' => [
                'capture_method' => 'manual',
            ],

            'success_url' =>
                'http://localhost:5173/successPayment?session_id={CHECKOUT_SESSION_ID}',

            'cancel_url' =>
                'http://localhost:5173/cancelPayment?session_id={CHECKOUT_SESSION_ID}',
        ]);

        return [
            'url' => $session->url,
            'stripe_session_id' => $session->id,
            'stripe_payment_intent_id' => $session->payment_intent,
        ];
    }

    public function capturePaymentIntent(
        string $paymentIntentId,
        int $amountToCapture
    ): void 
        {
            $paymentIntent = $this->stripe->paymentIntents->retrieve(
                $paymentIntentId
            );

            if ($amountToCapture > $paymentIntent->amount) {
                throw new \RuntimeException(
                    'Le montant à capturer est supérieur au montant autorisé par Stripe.'
                );
            }

            $this->stripe->paymentIntents->capture(
                $paymentIntentId,
                [
                    'amount_to_capture' => $amountToCapture,
                ]
            );
        }

    public function cancelPaymentIntent(string $paymentIntentId): void
    {
        $this->stripe->paymentIntents->cancel($paymentIntentId);
    }

    public function retrieveSession(
        string $sessionId
    ): \Stripe\Checkout\Session {
        return $this->stripe
            ->checkout
            ->sessions
            ->retrieve($sessionId);
    }
}