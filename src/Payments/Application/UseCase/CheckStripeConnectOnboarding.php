<?php

namespace App\Payments\Application\UseCase;

use App\Payments\Domain\Payment\StripeConnectGatewayInterface;
use App\Users\Domain\Entity\User;
use App\Users\Domain\Repository\UserRepositoryInterface;


final class CheckStripeConnectOnboarding
{
    public function __construct(
        private StripeConnectGatewayInterface $stripeConnectGateway,
        private UserRepositoryInterface $userRepository,
    ) {
    }

    public function execute(string $stripeAccountId): bool
{
    $user = $this->userRepository
        ->findOneByStripeAccount($stripeAccountId);

    if (!$user) {
        throw new \RuntimeException(
            'Utilisateur correspondant au compte Stripe introuvable.'
        );
    }

    $isOnboarded =
        $this->stripeConnectGateway
            ->isAccountOnboarded($stripeAccountId);

    $user->setStripeOnboarded($isOnboarded);

    $this->userRepository->save($user);

    return $isOnboarded;
}
}