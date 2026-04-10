<?php

namespace App\Payments\UI\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Stripe\Stripe;
use Stripe\Webhook;
use App\Orders\Domain\OrderRepositoryInterface; // ton repository pour créer la commande

class StripeWebhookController extends AbstractController
{
    private OrderRepositoryInterface $orderRepository;
    private string $stripeWebhookSecret;

    public function __construct(OrderRepositoryInterface $orderRepository, string $stripeWebhookSecret)
    {
        $this->orderRepository = $orderRepository;
        $this->stripeWebhookSecret = $stripeWebhookSecret;
    }

    #[Route('/stripe/webhook', name: 'stripe_webhook', methods: ['POST'])]
    public function handle(Request $request): Response
    {
        $payload = $request->getContent();
        $sigHeader = $request->headers->get('stripe-signature');

        try {
            // Vérifie que l'événement vient bien de Stripe
            $event = Webhook::constructEvent(
                $payload,
                $sigHeader,
                $this->stripeWebhookSecret
            );
        } catch (\UnexpectedValueException $e) {
            // Payload invalide
            return new Response('Invalid payload', 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Signature invalide
            return new Response('Invalid signature', 400);
        }

        // On ne traite que les paiements réussis
        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;

            // Récupération du panier depuis le metadata
            $cart = json_decode($session->metadata->cart, true);

            // Ici, tu peux créer ta commande en base
            foreach ($cart as $item) {
                $this->orderRepository->addItem(
                    $item['id'],
                    $item['title'],
                    $item['price'],
                    $item['quantity']
                );
            }

            // Valider / enregistrer la commande finale
            $this->orderRepository->save();
        }

        return new Response('Webhook handled', 200);
    }
}