<?php

namespace App\Payments\Application\UseCase;

use App\Payments\Domain\Payment\StripeConnectGatewayInterface;
use App\Users\Domain\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

final class CreateStripeConnectOnboardingLink
{
    public function __construct(
        private StripeConnectGatewayInterface $stripeConnectGateway,
        private EntityManagerInterface $entityManager
    ) {
    }

    public function execute(User $user): string
{
    $stripeAccountId = $user->getStripeConnectAccountId();

    if ($stripeAccountId === null) {

        $stripeAccountId =
            $this->stripeConnectGateway->createAccount($user);

        $user->setStripeConnectAccountId($stripeAccountId);
        $user->setStripeOnboarded(false);

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    $returnUrl = sprintf(
        'http://localhost:8000/api/stripe/connect/return?account=%s',
        urlencode($stripeAccountId)
    );

    return $this->stripeConnectGateway->createOnboardingLink(
        $stripeAccountId,
        'http://localhost:5173/stripe-connect/refresh',
        $returnUrl
    );
}
}