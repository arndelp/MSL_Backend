<?php

namespace App\Books\UI\Controller;

use App\Books\Application\DTO\BookDTO;
use App\Books\Application\UseCase\GetPersonnalBooksByApi;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Psr\Log\LoggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Books\Application\UseCase\RecordBookByApi;
use Throwable;
use Symfony\Component\HttpFoundation\Response;
use App\Books\Application\UseCase\GetAllBooks;
use Symfony\Bundle\SecurityBundle\Security;
use App\Users\Domain\Entity\User;
use App\Books\Domain\Repository\BookRepositoryInterface;
use App\Books\Application\UseCase\DeleteBook;

final class BookController extends AbstractController
{
    public function __construct(
        private Security $security,
        private BookRepositoryInterface $bookRepository,
    ) {}


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
                quantity: $data['quantity'] ?? null,
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

    public function Alls(GetAllBooks $getAllBooks): JsonResponse
    {
        $books = $getAllBooks->execute();
        return new JsonResponse($books);
    }

    public function getPersonnalBooks(GetPersonnalBooksByApi $getPersonnalBooksByApi): JsonResponse
    {
        $user = $this->security->getUser();

        if (!$user instanceof User) {
            return new JsonResponse(['error' => 'Utilisateur non authentifié'], 401);
        }

        $data = $getPersonnalBooksByApi->execute($user);

        return new JsonResponse($data);
    }
 
    public function delete(int $id, DeleteBook $deleteBook ): JsonResponse
    {
         $user = $this->security->getUser();

        if (!$user instanceof User) {
            return new JsonResponse(['error' => 'Utilisateur non authentifié'], 401);
        }

        try {
            $deleteBook->execute($id);

            return new JsonResponse([
                'success' => 'Livre supprimé avec succès'
            ], 200);

        } catch (\InvalidArgumentException $e) {
            return new JsonResponse([
                'error' => $e->getMessage()
            ], 404);

        } catch (\Throwable $e) {
            return new JsonResponse([
                'error' => 'Suppression du livre impossible, veuillez nous contacter',
            ], 500);
        }
    }

    public function detail(int $id): JsonResponse
{
    $book = $this->bookRepository->findById($id);

    if (!$book || $book->getStatus() !== 'available') {
        return new JsonResponse(['error' => 'Livre introuvable'], 404);
    }

    return new JsonResponse([
        'id' => $book->getId(),
        'title' => $book->getTitle(),
        'authorName' => $book->getAuthorName(),
        'price' => $book->getPrice(),
        'quantity' => $book->getQuantity(),
        'format' => $book->getFormat()?->value,
        'pageCount' => $book->getPageCount(),
        'description' => $book->getDescription(),
        'extract' => $book->getExtract(),
        'coverUrl' => $book->getCoverUrl(),
        'imageUrls' => $book->getImageUrls(),
        'categories' => array_map(fn ($cat) => [
            'id' => $cat->getId(),
            'name' => $cat->getName(),
        ], $book->getCategories()->toArray()),
    ]);
}
    
    
};
    

