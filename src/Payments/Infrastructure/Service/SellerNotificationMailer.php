<?php

namespace App\Payments\Infrastructure\Service;

use Symfony\Component\Mailer\MailerInterface;
use App\Payments\Domain\Service\SellerNotificationMailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use App\Orders\Domain\Entity\OrderItem;
use Symfony\Component\Mime\Address;





final class SellerNotificationMailer implements SellerNotificationMailerInterface
{
    public function __construct(
        private MailerInterface $mailer,     
    ) {}

    public function sendOrderConfirmation(OrderItem $orderItem): void
    {
     

       

        $acceptUrl = sprintf(
            '%s/seller/order-items/%d/accept?token=%s',
            $_ENV['FRONTEND_URL'],
            $orderItem->getId(),
            $orderItem->getConfirmationToken()
        );

        $refuseUrl = sprintf(
            '%s/seller/order-items/%d/refuse?token=%s',
            $_ENV['FRONTEND_URL'],
            $orderItem->getId(),
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
                'logoUrl' => $_ENV['APP_URL'].'/logo/Logo.png',
            ]);
            
            

       
     
        $this->mailer->send($email);
     
    }
}