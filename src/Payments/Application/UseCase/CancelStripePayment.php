<?php

namespace App\Payments\Application\UseCase;

use App\Payments\Domain\Payment\StripeGatewayInterface;

final class CancelStripePayment
{
    public function __construct(
        private StripeGatewayInterface $stripeGateway
    ) {}

    public function execute(string $paymentIntentId): void
    {
        $this->stripeGateway->cancelPaymentIntent($paymentIntentId);
    }
}
