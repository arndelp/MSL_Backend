<?php

namespace App\Payments\Application\UseCase;

use App\Orders\Domain\Entity\Order;
use App\Payments\Domain\Payment\StripeGatewayInterface;

final class CreateStripeSession
{
    public function __construct(
        private StripeGatewayInterface $stripeGateway
    ) {
    }

    public function execute(Order $order): array
    {
        return $this->stripeGateway->createSession($order);
    }
}