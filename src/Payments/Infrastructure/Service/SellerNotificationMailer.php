<?php

namespace App\Payments\Infrastructure\Service;

use Symfony\Component\Mailer\MailerInterface;
use App\Payments\Domain\Service\SellerNotificationMailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use App\Orders\Domain\Entity\OrderItem;
use Symfony\Component\Mime\Address;
use Symfony\Component\DependencyInjection\Attribute\Autowire;





final class SellerNotificationMailer implements SellerNotificationMailerInterface
{
    public function __construct(
        private MailerInterface $mailer,   
        #[Autowire('%env(FRONTEND_URL)%')]
        private string $frontendUrl,
        #[Autowire('%env(APP_URL)%')]
        private string $appUrl,  
    ) {}

    public function sendOrderConfirmation(OrderItem $orderItem): void
    {
       $logoUrl = sprintf(
        '%s/logo/Logo.png',
        $this->appUrl,
       );

        $acceptUrl = sprintf(
            '%s/seller/orderItems/%d/%s/accept?confirmationToken=%s',
            $this->frontendUrl,
            $orderItem->getId(),
            $orderItem->getBook()->getTitle(),
            $orderItem->getConfirmationToken()
        );

        $refuseUrl = sprintf(
            '%s/seller/orderItems/%d/%s/refuse?confirmationToken=%s',
            $this->frontendUrl,
            $orderItem->getId(),    
            $orderItem->getBook()->getTitle(),       
            $orderItem->getConfirmationToken()
        );
       
        $email = (new TemplatedEmail())
            ->from(new Address('admin@monsalondulivre.fr', 'Monsalondulivre.fr'))
            ->to($orderItem->getUser()->getEmail())
            ->subject('Nouvelle commande à confirmer')
            ->htmlTemplate('emails/ask_for_order_confirmation.html.twig')
            ->context([
                'orderItem' => $orderItem,
                'acceptUrl' => $acceptUrl,
                'refuseUrl' => $refuseUrl,
                'logoUrl' => $logoUrl,
            ]);       
     
        $this->mailer->send($email);     
    }
}