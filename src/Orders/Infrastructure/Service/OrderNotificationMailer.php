<?php

namespace App\Orders\Infrastructure\Service;

use Symfony\Component\Mailer\MailerInterface;
use App\Orders\Domain\Service\OrderNotificationMailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use App\Orders\Domain\Entity\OrderItem;
use Symfony\Component\Mime\Address;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final class OrderNotificationMailer implements OrderNotificationMailerInterface
{
    public function __construct(
        private MailerInterface $mailer,   
        #[Autowire('%env(FRONTEND_URL)%')]
        private string $frontendUrl,
    ){}

    //Email de prévention au vendeur si son stock === 0
    public function sendOutOfStockMail(OrderItem $orderItem): void
    {

    $changeStockUrl = sprintf(
        '%s/userAccount#userAccount',
        $this->frontendUrl,
    );

    $email = (new TemplatedEmail())
        ->from(new Address('automated@monsalondulivre.fr', 'Monsalondulivre.fr'))
        ->to($orderItem->getSeller()->getEmail())
        ->subject('Votre livre "' . $orderItem->getBook()->getTitle() . '" est désormais en rupture de stock')
        ->htmlTemplate('emails/out_of_stock.html.twig')
        ->context([
            'orderItem' => $orderItem,
            'changeStockUrl' => $changeStockUrl,
        ]);
        
    $this->mailer->send($email);
    }
/*
    public function sendOrderConfirmationEmailToBuyer(OrderItem $orderItem): void
    {
    $email = (new TemplatedEmail())
        ->from(new Address('automated@monsalondulivre.fr', 'Monsalondulivre.fr'))
        ->to($orderItem->getBuyerUser()->getEmail())
        ->subject('Confirmation de votre commande')
        ->htmlTemplate('emails/confirmated_order.html.twig')
        ->context([
            'orderItem' => $orderItem,
        ]);
    $this->mailer->send($email);
    }
*/
}
