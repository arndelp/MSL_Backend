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
use App\Books\Application\UseCase\GetBook;
use App\Books\Application\UseCase\ToBeVerified;
use App\Books\Application\UseCase\RejectBook;
use App\Books\Application\UseCase\GetDeletedBooks;
use App\Books\Application\UseCase\RemoveBook;
use App\Books\Application\UseCase\GetFilteredBooks;
use App\Books\Application\UseCase\GetDistinctFilterValues;
use App\Books\Application\DTO\BookFilterDTO;
use App\Books\UI\Form\BookFilteredType;





final class BookController extends AbstractController
{
    public function __construct(
        private Security $security,
        private BookRepositoryInterface $bookRepository,
        private GetAllBooks $getAllBooks,
        private GetAuthorNameAvailable $getAuthorNameAvailable,
        private GetAuthorNamesByUser $getAuthorNameByUser,
        private GetNotVerifiedBooks $getNotVerifiedBooks,
        private GetBook $getBook,
        private GetDeletedBooks $getDeletedBooks,
        private GetFilteredBooks $getFilteredBooks,
        private GetDistinctFilterValues $getDistinctFilterValues,        
        
    ) {}

    /*
    * Foonctions pour l'API (front-end))
    */

    //UTILISATION DU PROCESSOR POUR ENREGISTRER UN LIVRE

    public function Alls(
    GetAllBooks $getAllBooks,
    Request $request
): JsonResponse {

    $page = max(
        1,
        $request->query->getInt('page', 1)
    );

    $filter = new BookFilterDTO([
        'status' => 'available',
        'isVerified' => true,
        'authorName' => $request->query->get('author'),
        'category' => $request->query->get('category'),
        'search' => $request->query->get('search'),
    ]);

    $books = $getAllBooks->execute(
        $filter,
        $page
    );

    return new JsonResponse($books);
}

   

    //Récupérer les livres de l'auteur connecté
    public function getPersonnalBooks(GetPersonnalBooksByApi $getPersonnalBooksByApi): JsonResponse
    {
        $user = $this->security->getUser();

        if (!$user instanceof User) {
            return new JsonResponse(['error' => 'Utilisateur non authentifié'], 401);
        }

        $data = $getPersonnalBooksByApi->execute($user);

        return new JsonResponse($data);
    }
    // rendre un livre supprimmé (status->deleted)
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

    //page dérails d'un livre
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

    //Rendre un livre indisponible 
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

    //Mettre à jour le stock d'un livre de l'auteur
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




/* 
* FONCTIONS POUR L'ADMINISTRATION (back-office) 
*/



   //Récupérer les livres à vérifier
    public function getNotVerifiedBooks(GetNotVerifiedBooks $getNotVerifiedBooks): Response
    {
        $books = $this->getNotVerifiedBooks->execute();

        

        return $this->render('@Books/not_verified_books.html.twig', [
            'books' => $books,
        ]);
    }

    //Page de détails d'un livre à vérifier
    public function detailBookToBeVerified(GetBook $getBook, int $id): Response
    {
        $book = $getBook->execute($id);

        if (!$book) {
            $this->addFlash('error', "Le livre n'existe pas");
            return $this->redirectToRoute('books.not.verified');
        }

        return $this->render('@Books/details.html.twig', [
            'book' => $book,
        ]);
    }

    //Rendre un livre vérifié (diffusé)
    public function toBeVerified(int $id, ToBeVerified $toBeVerified): Response
    {
        try {
            $toBeVerified->execute($id);

            $this->addFlash('success', 'Livre vérifié avec succès');
            return $this->redirectToRoute('books.not.verified');

        } catch (\InvalidArgumentException $e) {
            $this->addFlash('error', $e->getMessage());
            return $this->redirectToRoute('books.not.verified');

        } catch (\Throwable $e) {
            $this->addFlash('error', 'Vérification du livre impossible');
            return $this->redirectToRoute('books.not.verified');
        }
    }

    //Rejeter un livre
    public function toBeRejected(int $id, RejectBook $rejectBook): Response
    {
        try {
            $rejectBook->execute($id);

            $this->addFlash('success', 'Livre rejeté avec succès');
            return $this->redirectToRoute('books.not.verified');

        } catch (\InvalidArgumentException $e) {
            $this->addFlash('error', $e->getMessage());
            return $this->redirectToRoute('books.not.verified');

        } catch (\Throwable $e) {
            $this->addFlash('error', 'Rejet du livre impossible');
            return $this->redirectToRoute('books.not.verified');
        }
    }

    //Récupérer les livres au status "deleted"
    public function getDeletedBooks(GetDeletedBooks $getDeletedBooks): Response
    {
        $books = $this->getDeletedBooks->execute();

        return $this->render('@Books/deleted_books.html.twig', [
            'books' => $books,
        ]);
    }

    //Supprimer un livre de la base de données
    public function removeBook(int $id, RemoveBook $removeBook): Response
    {
        try {
            $removeBook->execute($id);

            $this->addFlash('success', 'Livre supprimé avec succès');
            return $this->redirectToRoute('books.deleted');

        } catch (\InvalidArgumentException $e) {
            $this->addFlash('error', $e->getMessage());
            return $this->redirectToRoute('books.deleted');

        } catch (\Throwable $e) {
            $this->addFlash('error', 'Suppression du livre impossible');
            return $this->redirectToRoute('books.deleted');
        }
    }

    //Récupérer les livres en fonction des filtres (avec pagination)
    public function indexFiltered(Request $request): Response
    {
        // On récupère la page et la limite dans la query string
        $page = $request->query->get('page', 1);
        $limit = $request->query->get('limit', 10);

        // Récupération des Valeurs distincts pour les filtres dynamiques
        //on utitlise $this-> car la méthode est dans le constructeur et non en local
        $distinctValues = $this->getDistinctFilterValues->execute();  

       // Créer le DTO vide (sera rempli par le formulaire)
        $filter = new BookFilterDTO();

        //Créer le formulaire et le lier au DTO
        $form = $this->createForm(BookFilteredType::class, $filter, [
            'method' => 'GET',
            'status' => $distinctValues['status'],  
            'authorName' => $distinctValues['authorName'],          
        ]);

         //Remplir le DTO avec les valeurs GET si formulaire soumis
        $form->handleRequest($request);

        //Appel au useCaseFiltré
        $result = $this->getFilteredBooks->execute($filter, $page, $limit);

        //Rendu du template
        return $this->render('@Books/index_filtered.html.twig', [
            'books'         => $result['books'],
            'isPaginated'      => true,
            'nbrePage'         => $result['nbrePage'] ?? 1,
            'page'             => $result['currentPage'] ?? 1,
            'nbre'             => $limit,
            'filterForm'       => $form->createView(),
            'selectedStatus'   => $filter->status,       // pour Twig    
            'selectedAuthorName' => $filter->authorName,         // pour Twig 
        ]);
    }

    //Page de détails d'un livre
    public function booksDetails(int $id): Response
    {
        $book = $this->getBook->execute($id);

        return $this->render('@Books/details.list.html.twig', [
            'book' => $book
        ]);
    }

    


}

