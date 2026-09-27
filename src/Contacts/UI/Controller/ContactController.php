<?php

namespace App\Contacts\UI\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Contacts\Application\UseCase\GetPaginatedContact;
use App\Contacts\Application\UseCase\GetContact;
use App\Contacts\Application\UseCase\DeleteContact;
use App\Contacts\Application\UseCase\SendMailAnswer;



//Controller qui gère la réception , la suppression des message de contact

class ContactController extends AbstractController
{
    public function indexAlls(
        GetPaginatedContact $getPaginatedContact,
        int $page ,
        int $nbre 
        ): Response
    {
       
        
        // Appeler le useCase de filtre
        $result = $getPaginatedContact->execute($page, $nbre);
       
        return $this->render('@Contacts/index.html.twig', [
            'contacts' => $result['contacts'], 
            'isPaginated' => true,
            'nbrePage' => $result['nbrePage'],
            'page' => $result['currentPage'],
            'nbre' => $nbre
        ]);
    }
        
       
    public function details(GetContact $getContact, int $id ):Response          //initialisation à null
    {       

        $contact = $getContact->execute($id); //Récupère le contact par son ID       

    //si l'id n'existe pas
        if(!$contact){ 
            //message flash
            $this->addFlash(type: 'error', message: "Il n'y a pas de message"); 
            return $this->redirectToRoute('contacts.list.alls');      
        }
    //si l'id existe       
        
        return $this->render('@Contacts/details.html.twig', ['contact' => $contact]);     
        
    } 


    public function deleteContact(DeleteContact $deleteContact, GetContact $getContact, int $id): Response     {        
        
        $contact = $getContact->execute($id);
        
        // Si le message existe, on le supprime, sinon message inexistant
        if ($contact) {
            //Récupération de l'id et envoi au useCase de suppression
            $deleteContact->execute($id);

            $this->addFlash('success', "Le message a été supprimé avec succès");
        } else {
            $this->addFlash('error', "Message inexistant");
           
        }
        return $this->redirectToRoute('contacts.list.alls');
    }

    public function answerContact(GetContact $getContact, Request $request, int $id, SendMailAnswer $sendMail): Response
    {
        $contact = $getContact->execute($id);

        // Récupération du contenu du textarea
        $message = $request->request->get('message');

        // Vérification du message en premier
        if (empty($message)) {
            $this->addFlash('error', "Message inexistant");
            return $this->redirectToRoute('contacts.list.alls');
        }

        // Si le contact existe, on envoie le message
        if ($contact) {
            $email = $contact->getUser()->getEmail();
            $subject = $contact->getSubject();

            $sendMail->execute(
                from: 'Monsalondulivre.fr <automated@monsalondulivre.fr>',
                to: $email,
                subject: $subject,
                content: $message
            );

            $this->addFlash('success', "Le message a été envoyé");
        } else {
            $this->addFlash('error', "Contact inexistant");
        }

        return $this->redirectToRoute('contacts.list.alls');
    }

}