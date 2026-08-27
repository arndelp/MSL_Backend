<?php

namespace App\Payments\Infrastructure\Payment;

use App\Payments\Domain\Payment\StripeConnectGatewayInterface;
use App\Users\Domain\Entity\User;
use Stripe\StripeClient;

class StripeConnectGateway implements StripeConnectGatewayInterface
{
    public function __construct(
        private StripeClient $stripe
    ) {
    }

    public function createAccount(User $user): string
    {
        
        
        $account = $this->stripe->accounts->create([
            'type' => 'express',
            'country' => 'FR',
            'email' => $user->getEmail(),
        ]);
       

        return $account->id;
    }

    public function createOnboardingLink(
        string $stripeConnectAccountId,
        string $refreshUrl,
        string $returnUrl
    ): string {
         
        $accountLink = $this->stripe->accountLinks->create([
            'account' => $stripeConnectAccountId,
            'refresh_url' => $refreshUrl,
            'return_url' => $returnUrl,
            'type' => 'account_onboarding',
        ]);

        return $accountLink->url;
    }

    public function isAccountOnboarded(
        string $stripeConnectAccountId
    ): bool {

        $account = $this->stripe->accounts->retrieve(
            $stripeConnectAccountId
        );

        return
            $account->details_submitted
            && $account->charges_enabled
            && $account->payouts_enabled;
    }

    public function createTransfer(
    int $amount,
    string $currency,
    string $stripeConnectAccountId,
    string $paymentIntentId
): string {

    $paymentIntent = $this->stripe->paymentIntents->retrieve(
        $paymentIntentId
    );

    if (!$paymentIntent->latest_charge) {
        throw new \RuntimeException(
            'Aucun Charge Stripe trouvé pour ce PaymentIntent.'
        );
    }

    $transfer = $this->stripe->transfers->create([
        'amount' => $amount,
        'currency' => strtolower($currency),
        'destination' => $stripeConnectAccountId,
        'source_transaction' => $paymentIntent->latest_charge,
    ]);

    return $transfer->id;
}


}