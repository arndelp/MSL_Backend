<?php

namespace App\Payments\Infrastructure\Payment;

use Stripe\StripeClient;
use App\Payments\Domain\Payment\StripeGatewayInterface;

class StripeGateway implements StripeGatewayInterface
{
    private StripeClient $stripe;

    public function __construct(StripeClient $stripe)
    {
        $this->stripe = $stripe;
    }

    public function createSession(array $data): string
    {
        $lineItems = [];

        $cart = $data['cart'] ?? [];
        $books = $data['books']['books'] ?? $data['books'] ?? []; // 🧪 Debug utile

       foreach ($cart as $cartEntry) {
    foreach ($cartEntry['items'] as $item) {
        // 🔎 trouver le livre correspondant
        $book = array_values(array_filter(
            $books,
            fn($b) => ($b['bookId'] ?? null) === ($item['bookId'] ?? null)
        ))[0] ?? null;

        if (!$book) continue;

        $lineItems[] = [
            'price_data' => [
                'currency' => 'eur',
                'product_data' => [
                    'name' => $book['title'],
                ],
                'unit_amount' => (int) ($book['price'] * 100), // ⚡ Stripe attend des centimes
            ],
            'quantity' => (int) $item['quantity'],
        ];
    }
}

// 🧪 Debug : vérifier si lineItems est rempli
if (empty($lineItems)) {
    throw new \Exception("lineItems vide ! Vérifie cart et books.");
}

// Créer la session Stripe
$session = $this->stripe->checkout->sessions->create([
    'payment_method_types' => ['card'],
    'mode' => 'payment',
    'line_items' => $lineItems,
    'payment_intent_data' => [
            'capture_method' => 'manual', // autorisation seulement
        ],
    'success_url' => 'http://localhost:5173/successPayment',
    'cancel_url' => 'http://localhost:5173/cancelPayment',
]);

return $session->url;
    }
}