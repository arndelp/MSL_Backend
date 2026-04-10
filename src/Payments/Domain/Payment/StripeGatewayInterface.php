<?php

// src/Infrastructure/Payment/StripeGatewayInterface.php
namespace App\Payments\Domain\Payment;



interface StripeGatewayInterface 
{
    public function createSession(array $cart): string; // Retourne l'ID de session Stripe
}