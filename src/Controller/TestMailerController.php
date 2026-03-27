<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class TestMailerController extends AbstractController
{
    #[Route('/test-mail', name: 'test_mail')]
    public function sendTestMail(MailerInterface $mailer): Response
    {
        $email = (new Email())
            ->from('admin@monsalondulivre.fr') // ton adresse IONOS exacte
            ->to('arndelp@yahoo.fr') // où tu veux recevoir le test
            ->subject('Test SMTP IONOS')
            ->text('Ceci est un test d’envoi depuis Symfony via IONOS.');

        $mailer->send($email);

        return new Response('Mail envoyé ! Vérifie ta boîte.');
    }
}