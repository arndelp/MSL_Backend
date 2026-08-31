<?php

namespace App\Contacts\UI\Controller;

use App\Contacts\Application\DTO\ContactMessageInputDTO;
use App\Contacts\Application\UseCase\SendMail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Psr\Log\LoggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Throwable;
use Symfony\Component\HttpFoundation\Response;
use App\Contacts\Application\UseCase\RecordContactMessageByApi;
use App\Users\Domain\Entity\User;




final class ContactMessageController extends AbstractController
{
   
  

    public function record(
        
        Request $request,        
        LoggerInterface $logger,
        ValidatorInterface $validator,
        RecordContactMessageByApi $recordContactMessageByApi,
        SendMail $sendMail,
       
    ): Response {



            $user = $this->getUser();

            if (!$user instanceof User) {
                return new JsonResponse(['error' => 'Utilisateur non authentifié'], 401);
            }               
        
            // Récupération des données JSON à partir de la requête
            $data = json_decode($request->getContent(), true);          
            
            if (empty($data)) {
                return new JsonResponse(['error' => 'Données reçues vides'], 400);
            }

            // Création du DTO à partir des données reçues
            $dto = new ContactMessageInputDTO(
                subject: $data['subject'] ?? null,
                content: $data['content'] ?? null
            );
                     
            // Validation
            $errors = $validator->validate($dto);
            if (count($errors) > 0) {
                return new JsonResponse(['errors' => (string) $errors], 400);
            }


        
            try {
                //délègue au use case pour enregistrer le message de contact
                $contactMessage = $recordContactMessageByApi->execute($dto);

                $sendMail->execute(
                    from: 'Monsalondulivre.fr <automated@monsalondulivre.fr>',   
                    to: 'arndelp80@gmail.com',
                    subject: $contactMessage->getSubject(),
                    content: $contactMessage->getContent()
                );
               
                $sendMail->execute(
                    from: 'Monsalondulivre.fr <automated@monsalondulivre.fr>',   
                    to: $user->getEmail(),
                    subject: $contactMessage->getSubject(),
                    content: $contactMessage->getContent()
                );

                return new JsonResponse(['success' => 'Message de contact enregistré avec succès'], 201);

            } catch (\InvalidArgumentException $e) {
                // Erreurs métier (ex: message de contact déjà enregistré)
                return new JsonResponse(['error' => $e->getMessage()], 400);

            } catch (Throwable $e) {
                //Erreurs serveur
                $logger->error('Erreur en recevant le nouveau message de contact : ' . $e->getMessage(), [
                    'exception' => $e,
                    'trace' => $e->getTraceAsString()
                ]);
                return new JsonResponse(['error' => 'Erreur interne serveur.'], 500);
            }          
    }
};
    

