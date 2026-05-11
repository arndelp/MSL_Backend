<?php

namespace App\Contacts\Infrastructure\Service;

use Symfony\Component\Mailer\MailerInterface;
use App\Contacts\Domain\Service\ContactMailerServiceInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;




class ContactMailerService implements ContactMailerServiceInterface
{
    public function __construct(
        private MailerInterface $mailer,
        private string $receiverEmail,
      
    )
    {}

    public function send(string $from,string $to,  string $subject, string $content): void
    {
        $email = (new TemplatedEmail())
            ->from($from)
            ->to($to)            
            ->subject($subject)
            ->htmlTemplate('emails/contact_message.html.twig')
            ->context([
                'subject' => $subject,
                'content' => $content,
                'to' => $to
            ]);

       
     
         $this->mailer->send($email);
    }
}