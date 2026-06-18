<?php

namespace App\Payments\UI\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Payments\Application\UseCase\CreateStripeSession;
use App\Orders\Domain\Entity\Order;
use App\Orders\Domain\Repository\OrderRepositoryInterface;
use App\Users\Domain\Entity\User;

class PaymentController extends AbstractController
{
    private CreateStripeSession $createStripeSession;
    private OrderRepositoryInterface $orderRepository;

    public function __construct(
        CreateStripeSession $createStripeSession,
        OrderRepositoryInterface $orderRepository
        )
    {
        $this->createStripeSession = $createStripeSession;
        $this->orderRepository = $orderRepository;
        
    }

    public function create(Request $request): JsonResponse
{
    $user = $this->getUser();

    if (!$user instanceof User) {
        return new JsonResponse(['error' => 'Utilisateur non authentifié'], 401);
    }

    $cartData = json_decode($request->getContent(), true);

    if (empty($cartData) || empty($cartData['cart'] ?? $cartData['order_items'])) {
        return new JsonResponse(['error' => 'Données reçues vides'], 400);
    }

    // 1) Création session Stripe
    $cart = $cartData['cart'] ?? $cartData['order_items'];
$session = $this->createStripeSession->execute($cart);

    // 2) Retourner uniquement l’URL Stripe
    return $this->json([
        'url' => $session['url']
    ]);
}
}