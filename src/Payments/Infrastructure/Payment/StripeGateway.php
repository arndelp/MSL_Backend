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

    public function createSession(array $data): string
    {
        $lineItems = [];

        $cart = $data['cart'] ?? [];
       

        foreach ($cart as $cartEntry) {
            foreach ($cartEntry['items'] as $item) {
                $id = intval($item['id'] ?? 0);
                $quantity = intval($item['quantity'] ?? 0);
        
                if (!$id || $quantity <= 0) continue;

                $title = $this->bookRepository->findTitleById($id);
                $price = $this->bookRepository->findPriceById($id);

                if ($title === null || $price === null) {
                continue;
                }

            $lineItems[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $title,
                    ],
                    'unit_amount' => (int)$price * 100, // ⚡ Stripe attend des centimes
                ],
                'quantity' => $quantity,
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