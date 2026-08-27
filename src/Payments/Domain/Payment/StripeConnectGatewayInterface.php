<?php

namespace App\Payments\Domain\Payment;

use App\Users\Domain\Entity\User;


interface StripeConnectGatewayInterface
{
    //Création d'un compte Connect pour un vendeur
    public function createAccount(User $user): string;

    //Création d'un lien de onboarding pour un vendeur
    public function createOnboardingLink(
        string $stripeConnectAccountId,
        string $refreshUrl,
        string $returnUrl
    ): string;

    //Vérification que le compte Connect est fonctionnel
    public function isAccountOnboarded(
        string $stripeConnectAccountId
    ): bool;

    //Création du tranfert vers le compte client
    public function createTransfer(
        int $amount,
        string $currency,
        string $stripeConnectAccountId,
        string $paymentIntentId
    ): string;

   
}