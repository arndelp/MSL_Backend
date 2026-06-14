<?php

namespace App\Payments\UI\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Payments\Application\UseCase\CreateStripeSession;
use App\Orders\Domain\Entity\Order;
use App\Orders\Domain\Repository\OrderRepositoryInterface;

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
       

        $cartData = json_decode($request->getContent(), true); // Récupère les données du panier envoyées depuis le frontend

        // 1) Création session Stripe
        $session = $this->createStripeSession->execute($cartData); // Appel du use case pour créer une session Stripe et récupérer l'ID de session

               

        // 2) Retourner uniquement l’URL Stripe
        return $this->json([
        'url' => $session['url']
    ]);
    }
}