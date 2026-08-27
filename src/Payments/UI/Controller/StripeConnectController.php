<?php

namespace App\Payments\UI\Controller;

use App\Payments\Application\UseCase\CreateStripeConnectOnboardingLink;
use App\Users\Domain\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use App\Payments\Application\UseCase\CheckStripeConnectOnboarding;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

final class StripeConnectController
{
    public function __construct(
        private Security $security,
        private CreateStripeConnectOnboardingLink $createOnboardingLink,
        private CheckStripeConnectOnboarding $checkStripeConnectOnboarding
    ) {
    }

    public function onboarding(): JsonResponse
    {
        
        $user = $this->security->getUser();

        if (!$user instanceof User) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Utilisateur non authentifié'
            ], 401);
        }

        $url = $this->createOnboardingLink->execute($user);

        return new JsonResponse([
            'success' => true,
            'url' => $url
        ]);
    }

   public function returnFromStripe(
        Request $request
    ): Response {

        $stripeAccountId = $request->query->get('account');

        if (!$stripeAccountId) {
            throw new \RuntimeException(
                'Identifiant du compte Stripe manquant.'
            );
        }

        $isOnboarded =
            $this->checkStripeConnectOnboarding
                ->execute($stripeAccountId);

        if ($isOnboarded) {
            return new RedirectResponse(
                'http://localhost:5173/stripe-connect/success'
            );
        }

        return new RedirectResponse(
            'http://localhost:5173/stripe-connect/incomplete'
        );
    }
}