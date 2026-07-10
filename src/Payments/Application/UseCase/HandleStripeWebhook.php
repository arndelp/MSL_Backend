<?php

namespace App\Payments\Application\UseCase;

use App\Orders\Domain\Repository\OrderItemRepositoryInterface;
use App\Enum\OrderItemStatus;
use Symfony\Component\Mailer\MailerInterface;

final class HandleStripeWebhook
{
    public function __construct(
        private OrderItemRepositoryInterface $orderItemRepository,
        private MailerInterface $mailer,
    ) {
    }

    public function execute(\Stripe\Event $event): void
    {
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

                    // ici tu enverras ton mail vendeur
                }

                $this->orderItemRepository->saveAll($orderItems);

                break;
        }
    }
}