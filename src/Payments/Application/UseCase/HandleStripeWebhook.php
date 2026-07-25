<?php

namespace App\Payments\Application\UseCase;

use App\Orders\Domain\Repository\OrderItemRepositoryInterface;
use App\Enum\OrderItemStatus;
use App\Payments\Domain\Service\SellerNotificationMailerInterface;
use Psr\Log\LoggerInterface;


final class HandleStripeWebhook
{
    public function __construct(
        private OrderItemRepositoryInterface $orderItemRepository,
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


                $orderItems = $this->orderItemRepository
                    ->findByStripeSessionId($session->id);

               

                foreach ($orderItems as $item) {

                    $item->setStripePaymentIntentId(
                        $session->payment_intent
                    );

                    $item->setStatus(
                        OrderItemStatus::PENDING_AUTHOR_CONFIRMATION
                    );

                    $item->setUpdatedAt(
                        new \DateTimeImmutable()
                    );    

                    if ($item->getConfirmationToken() === null) {
                        $item->setConfirmationToken(
                            bin2hex(random_bytes(32))
                        );

                        $item->setConfirmationTokenExpiresAt(
                            new \DateTimeImmutable('+7 days')
                        );
                    }
                    
                    
                }

                $this->orderItemRepository->saveAll($orderItems);

                foreach ($orderItems as $item) {                   

                    $this->sellerNotification->sendOrderConfirmation($item);
                }

                break;
        }
    }
}