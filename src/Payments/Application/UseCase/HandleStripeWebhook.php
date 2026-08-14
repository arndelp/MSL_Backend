<?php

namespace App\Payments\Application\UseCase;

use App\Enum\SellerPaymentStatus;
use App\Orders\Domain\Repository\OrderRepositoryInterface;
use App\SellerPayments\Domain\Service\SellerNotificationMailerInterface;
use Psr\Log\LoggerInterface;

final class HandleStripeWebhook
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private SellerNotificationMailerInterface $sellerNotification,
        private LoggerInterface $logger,
    ) {
    }

    public function execute(\Stripe\Event $event): void
    {
        $this->logger->info('Stripe webhook reçu', [
            'type' => $event->type,
        ]);

        switch ($event->type) {

            case 'checkout.session.completed':

                /** @var \Stripe\Checkout\Session $session */
                $session = $event->data->object;



               $this->logger->info('Checkout Session Stripe', [
                'session_id' => $session->id,
                'payment_intent' => $session->payment_intent,
                'payment_status' => $session->payment_status,
                'status' => $session->status,
            ]);

                /*
                 * Recherche de la commande
                 */
                $order = $this->orderRepository
                    ->findByStripeSessionId($session->id);

                if (!$order) {

                    $this->logger->error(
                        'Commande introuvable pour la session Stripe',
                        [
                            'stripe_session_id' => $session->id,
                        ]
                    );

                    return;
                }

                /*
                 * Enregistrement du PaymentIntent
                 *
                 * Il est disponible à ce stade.
                 */
                $order->setStripePaymentIntentId(
                    $session->payment_intent
                );

                
                $order->setUpdatedAt(
                    new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris'))
                );

                /*
                 * Traitement des OrderItems
                 */
                foreach ($order->getOrderItems() as $item) {

                    /*
                     * Réservation du stock
                     */
                    $item->getBook()->reserve(
                        $item->getQuantity()
                    );            

                    
                }

                foreach ($order->getSellerPayments() as $payment) {
                    $payment->setStatus(
                        SellerPaymentStatus::WAITING_SELLER_CONFIRMATION
                    );

                    $payment->setUpdatedAt(
                        new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris'))
                    );

                    /*
                     * Création du token de confirmation
                     */
                    if ($payment->getConfirmationToken() === null) {

                        $payment->setConfirmationToken(
                            bin2hex(random_bytes(32))
                        );

                        $payment->setConfirmationTokenExpiresAt(
                            new \DateTimeImmutable('+6 days', new \DateTimeZone('Europe/Paris'))
                        );
                    }
              
            }
             /*
                 * Sauvegarde de la commande,
                 * des OrderItems et du PaymentIntent.
                 */
                $this->orderRepository->save($order);

                /*
                 * Notification des vendeurs.  
                 */
                foreach ($order->getSellerPayments() as $payment) {



                     $this->sellerNotification
                        ->sendOrderConfirmation($payment);
                 }

                break;
                }
        }
}