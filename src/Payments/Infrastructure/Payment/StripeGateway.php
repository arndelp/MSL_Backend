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

    $cart = $data['cart'] ?? [];

    foreach ($cart as $cartEntry) {

        // CAS 1 : $cartEntry est un OrderItemDTO
        if ($cartEntry instanceof \App\Orders\Application\DTO\OrderItemDTO) {

            $id = intval($cartEntry->book_id ?? 0);
            $quantity = intval($cartEntry->quantity ?? 0);

            if ($id && $quantity > 0) {
                $title = $this->bookRepository->findTitleById($id);
                $price = $this->bookRepository->findPriceById($id);

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

            continue;
        }

        // CAS 2 : $cartEntry est un array avec 'items'
        if (is_array($cartEntry) && isset($cartEntry['items'])) {

            foreach ($cartEntry['items'] as $item) {

                // item = array
                $id = intval($item['id'] ?? 0);
                $quantity = intval($item['quantity'] ?? 0);

                if ($id && $quantity > 0) {
                    $title = $this->bookRepository->findTitleById($id);
                    $price = $this->bookRepository->findPriceById($id);

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
        'success_url' => 'http://localhost:5173/successPayment',
        'cancel_url' => 'http://localhost:5173/cancelPayment',
    ]);

    return [
        'url' => $session->url,
        'stripe_session_id' => $session->id,
        'stripe_payment_intent_id' => $session->payment_intent,
    ];
}

    public function capturePaymentIntent(string $paymentIntentId): void
    {
        $this->stripe->paymentIntents->capture($paymentIntentId);
    }

    public function cancelPaymentIntent(string $paymentIntentId): void
    {
        $this->stripe->paymentIntents->cancel($paymentIntentId);
    }
}
