<?php

// src/Infrastructure/Payment/StripeGatewayInterface.php
namespace App\Payments\Domain\Payment;



interface StripeGatewayInterface 
{
    public function createSession(array $cart): array; // Retourne l'ID de session Stripe
     /**
     * Crée une session Stripe Checkout (paiement en mode manual capture)
     *
     * @param array $data
     * @return array [
     *     'url' => string,
     *     'stripe_session_id' => string,
     *     'stripe_payment_intent_id' => string|null
     * ]
     */

    public function capturePaymentIntent(string $paymentIntentId): void;
    /**
     * Capture un PaymentIntent (débit réel du client)
     *
     * @param string $paymentIntentId
     * @return void
     */

    public function cancelPaymentIntent(string $paymentIntentId): void;
    /**
     * Annule un PaymentIntent (libère l’autorisation)
     *
     * @param string $paymentIntentId
     * @return void
     */


}