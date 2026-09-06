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
use App\Books\Application\UseCase\ToBeUnavailable;
use App\Books\Application\UseCase\ToChangeStock;
use App\Books\Application\UseCase\GetAuthorNameAvailable;
use App\Books\Application\UseCase\GetAuthorNamesByUser;
use App\Books\Application\UseCase\GetNotVerifiedBooks;

final class BookController extends AbstractController
{
    public function __construct(
        private Security $security,
        private BookRepositoryInterface $bookRepository,
        private GetAllBooks $getAllBooks,
        private GetAuthorNameAvailable $getAuthorNameAvailable,
        private GetAuthorNamesByUser $getAuthorNameByUser,
        private GetNotVerifiedBooks $getNotVerifiedBooks
    ) {}

    //UTILISATION DU PROCESSOR POUR ENREGISTRER UN LIVRE

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
            'quantityAvailable' => $book->getQuantityAvailable(),
            'format' => $book->getFormat()?->value,
            'pageCount' => $book->getPageCount(),
            'description' => $book->getDescription(),
            'extract' => $book->getExtract(),
            'coverUrl' => $book->getCoverUrl(),
            'imageUrls' => $book->getImageUrls(),
            'categories' => 
                array_map(fn ($cat) => [
                    'id' => $cat->getId(),
                    'name' => $cat->getName(),
                ], $book->getCategories()->toArray()),
            
        ]);
    }

    public function toBeUnavailable(int $id, ToBeUnavailable $toBeUnavailable): JsonResponse
    {
         $user = $this->security->getUser();

        if (!$user instanceof User) {
            return new JsonResponse(['error' => 'Utilisateur non authentifié'], 401);
        }

        try {
            $toBeUnavailable->execute($id);

            return new JsonResponse([
                'success' => 'Livre mis à jour avec succès'
            ], 200);

        } catch (\InvalidArgumentException $e) {
            return new JsonResponse([
                'error' => $e->getMessage()
            ], 404);

        } catch (\Throwable $e) {
            return new JsonResponse([
                'error' => 'Mise à jour du livre impossible, veuillez nous contacter',
            ], 500);
        }
    }

    public function toUpdateStock(int $id, int $quantity, ToChangeStock $toChangeStock): JsonResponse
    {
        $user = $this->security->getUser();

        if (!$user instanceof User) {
            return new JsonResponse(['error' => 'Utilisateur non authentifié'], 401);
        }

        try {
            
            $toChangeStock->execute($id, $quantity);

            return new JsonResponse([
                'success' => 'Quantité du livre mise à jour avec succès'
            ], 200);

        } catch (\InvalidArgumentException $e) {
            return new JsonResponse([
                'error' => $e->getMessage()
            ], 404);

        } catch (\Throwable $e) {
            return new JsonResponse([
                'error' => 'Mise à jour de la quantité du livre impossible, veuillez nous contacter',
            ], 500);
        }
    }

    //Récupérer tout les noms d'auteur de livre disponible dans l'ordre alphabétique (pour le filtre de recherche))
    public function getAllAuthorNames(GetAuthorNameAvailable $authorNames): JsonResponse
    {
       $authorNames= $this->getAuthorNameAvailable->execute();

       return $this->json($authorNames, 200, [], ['groups' => 'authorNames:read']);
    }
    
    //Récupérer les noms d'auteur utilisés en fonction de l'user connecté (pour le filtre de recherche))
    public function getAuthorNamesByUser(GetAuthorNamesByUser  $authorNames): JsonResponse
    {
        $user = $this->security->getUser();

        if (!$user instanceof User) {
            return new JsonResponse(['error' => 'Utilisateur non authentifié'], 401);
        }

        $authorNames = $this->getAuthorNameByUser->execute($user);
       

        return $this->json($authorNames, 200, [], ['groups' => 'authorNames:read']);
    }

    public function getNotVerifiedBooks(GetNotVerifiedBooks $getNotVerifiedBooks): Response
    {
        $books = $this->getNotVerifiedBooks->execute();

        

        return $this->render('@Books/not_verified_books.html.twig', [
            'books' => $books,
        ]);
    }
}

