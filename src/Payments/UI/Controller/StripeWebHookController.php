<?php

namespace App\Payments\UI\Controller;

use App\Payments\Application\UseCase\HandleStripeWebhook;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class StripeWebhookController extends AbstractController
{
    public function __construct(
        private HandleStripeWebhook $handleStripeWebhook,
        private string $stripeWebhookSecret,
    ) {
    }

    public function __invoke(Request $request): Response
    {
 
         
      
        $payload = $request->getContent();
        $signature = $request->headers->get('Stripe-Signature');

        try {

            $event = Webhook::constructEvent(
                $payload,
                $signature,
                $this->stripeWebhookSecret
            );

        } catch (\UnexpectedValueException $e) {

            return new Response('Invalid payload', 400);

        } catch (SignatureVerificationException $e) {

            return new Response('Invalid signature', 400);

        }

       

        $this->handleStripeWebhook->execute($event);

        return new Response('OK', 200);
    }
}