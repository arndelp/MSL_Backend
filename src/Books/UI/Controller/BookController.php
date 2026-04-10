<?php

namespace App\Books\UI\Controller;

use App\Books\Application\DTO\BookDTO;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Psr\Log\LoggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Books\Application\UseCase\RecordBookByApi;
use Throwable;
use Symfony\Component\HttpFoundation\Response;

final class BookController extends AbstractController
{
   
   

    public function record(
        Request $request,        
        LoggerInterface $logger,
        ValidatorInterface $validator,
        RecordBookByApi $recordBookByApi,
    ): Response {
        {
            // Récupération des données JSON
            $data = json_decode($request->getContent(), true); 
       
            $logger->info('Données reçues', ['data' => $data]);
            
            if (empty($data)) {
            return new JsonResponse(['error' => 'Données reçues vides'], 400);
            }

            $dto = new BookDTO(
                title: $data['title'] ?? null,   
                authorName: $data['authorName'] ?? null,         
                price: $data['price'] ?? null,
                stock: $data['stock'] ?? null,
                format: $data['format'] ?? null,
                description: $data['description'] ?? null,
                isbn: $data['isbn'] ?? null,
                pageCount: $data['pageCount'] ?? null,
                currency: $data['currency'] ?? null,
                categories: $data['categories'] ?? []
            
            );
            
            // Validation
            $errors = $validator->validate($dto);
            if (count($errors) > 0) {
                return new JsonResponse(['errors' => (string) $errors], 400);
            }

        
            try {
                //délègue au use case pour enregistrer le livre
                $book = $recordBookByApi->execute($dto);

                return new JsonResponse(['success' => 'Livre enregistré avec succès'], 201);

            } catch (\InvalidArgumentException $e) {
                // Erreurs métier (ex: livre déjà enregistré)
                return new JsonResponse(['error' => $e->getMessage()], 400);

            } catch (Throwable $e) {
                //Erreurs serveur
                $logger->error('Erreur en recevant le nouveau client : ' . $e->getMessage(), [
                    'exception' => $e,
                    'trace' => $e->getTraceAsString()
                ]);
                return new JsonResponse(['error' => 'Erreur interne serveur.'], 500);
            }
        

        
        

            
            
        }
    }
};
    

