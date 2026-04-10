<?php

namespace App\Payments\UI\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Payments\Application\UseCase\CreateStripeSession;

class PaymentController extends AbstractController
{
    private CreateStripeSession $createStripeSession;

    public function __construct(CreateStripeSession $createStripeSession)
    {
        $this->createStripeSession = $createStripeSession;
    }

    public function create(Request $request): JsonResponse
    {
       

        $cartData = json_decode($request->getContent(), true); // Récupère les données du panier envoyées depuis le frontend

        $session = $this->createStripeSession->execute($cartData); // Appel du use case pour créer une session Stripe et récupérer l'ID de session

        return $this->json([
        'url' => $session // ⚠️ ici on retourne l'URL Stripe
    ]);
    }
}