<?php

namespace App\Payments\Infrastructure\Payment;

use Stripe\StripeClient;
use App\Payments\Domain\Payment\StripeGatewayInterface;
use App\Books\Domain\Repository\BookRepositoryInterface;


class StripeGateway implements StripeGatewayInterface
{
    private StripeClient $stripe;
    private BookRepositoryInterface $bookRepository;

    public function __construct(StripeClient $stripe, BookRepositoryInterface $bookRepository)
    {
        $this->stripe = $stripe;
        $this->bookRepository = $bookRepository;
    }

    public function createSession(array $data): array
        {
            $lineItems = [];

        
                $cart = $data['cart'] ?? $data;

        foreach ($cart as $cartEntry) {

            if (!is_array($cartEntry)) {
                continue;
            }

    // Accepte id OU book_id
    $bookId = $cartEntry['id'] ?? $cartEntry['book_id'] ?? null;
    $quantity = $cartEntry['quantity'] ?? null;

    if ($bookId && $quantity > 0) {

        $title = $this->bookRepository->findTitleById($bookId);
        $price = $this->bookRepository->findPriceById($bookId);

        if ($title !== null && $price !== null) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => ['name' => $title],
                    'unit_amount' => (int)$price * 100,
                ],
                'quantity' => $quantity,
            ];
        }
    }
}

if (empty($lineItems)) {
    throw new \Exception("lineItems vide ! Le format de cart ne correspond pas.");
}


    $session = $this->stripe->checkout->sessions->create([
        'payment_method_types' => ['card'],
        'mode' => 'payment',
        'line_items' => $lineItems,
        'payment_intent_data' => [
            'capture_method' => 'manual',
        ],
        'success_url' => 'http://localhost:5173/successPayment?session_id={CHECKOUT_SESSION_ID}', 
        'cancel_url' => 'http://localhost:5173/cancelPayment?session_id={CHECKOUT_SESSION_ID}',
    ]);

    return [
        'url' => $session->url,
        'stripe_session_id' => $session->id,
        'stripe_payment_intent_id' => $session->payment_intent,
    ];
}

//Capture du payement intent pour finaliser le paiement
    public function capturePaymentIntent(string $paymentIntentId): void
    {
        $this->stripe->paymentIntents->capture($paymentIntentId);
    }

//Annulation du payement intent pour annuler le paiement
    public function cancelPaymentIntent(string $paymentIntentId): void
    {
        $this->stripe->paymentIntents->cancel($paymentIntentId);
    }

//Récupération de la session de paiement pour vérifier le statut du paiement (non utilisé pour le moment)
    public function retrieveSession(string $sessionId): \Stripe\Checkout\Session
    {
        return $this->stripe->checkout->sessions->retrieve($sessionId);
    }
}
