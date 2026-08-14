<?php

namespace App\SellerPayments\Infrastructure\Service;

use Symfony\Component\Mailer\MailerInterface;
use App\SellerPayments\Domain\Service\SellerNotificationMailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use App\SellerPayments\Domain\Entity\SellerPayment;
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

    public function sendOrderConfirmation(SellerPayment $sellerPayment): void
    {
        //logo car base d'email non encore créée
       $logoUrl = sprintf(
        '%s/logo/Logo.png',
        $this->appUrl,
       );

        $acceptUrl = sprintf(
            '%s/seller/sellerPayment/%d/%s/accept?confirmationToken=%s',
            $this->frontendUrl,
            $sellerPayment->getId(),
            $sellerPayment->getPaymentNumber(),
            $sellerPayment->getConfirmationToken()
        );

        $refuseUrl = sprintf(
            '%s/seller/sellerPayment/%d/%s/refuse?confirmationToken=%s',
            $this->frontendUrl,
            $sellerPayment->getId(),    
            $sellerPayment->getPaymentNumber(),       
            $sellerPayment->getConfirmationToken()
        );
       
        $email = (new TemplatedEmail())
            ->from(new Address('admin@monsalondulivre.fr', 'Monsalondulivre.fr'))
            ->to($sellerPayment->getSeller()->getEmail())
            ->subject('Nouvelle commande à confirmer')
            ->htmlTemplate('emails/ask_for_order_confirmation.html.twig')
            ->context([
                'sellerPayment' => $sellerPayment,
                'acceptUrl' => $acceptUrl,
                'refuseUrl' => $refuseUrl,
                'logoUrl' => $logoUrl,
            ]);       
     
        $this->mailer->send($email);     
    }
}