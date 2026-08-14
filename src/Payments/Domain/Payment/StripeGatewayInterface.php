<?php

namespace App\Payments\Domain\Payment;

use App\Orders\Domain\Entity\Order;

interface StripeGatewayInterface
{
    public function createSession(Order $order): array;

    public function capturePaymentIntent(
        string $paymentIntentId, 
        int $amountToCapture
    ): void;

    public function cancelPaymentIntent(string $paymentIntentId): void;

    public function retrieveSession(string $sessionId): \Stripe\Checkout\Session;
}