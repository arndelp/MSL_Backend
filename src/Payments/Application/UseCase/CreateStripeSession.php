<?php


namespace App\Payments\Application\UseCase;

use App\Payments\Domain\Payment\StripeGatewayInterface;

final class CreateStripeSession
{
    private StripeGatewayInterface $stripeGateway;

    public function __construct(StripeGatewayInterface $stripeGateway)
    {
        $this->stripeGateway = $stripeGateway;
    }

    public function execute(array $cart): array
    {
        // Ici tu peux faire des règles de calcul, validation, totaux, etc.
        return $this->stripeGateway->createSession($cart);
    }
}