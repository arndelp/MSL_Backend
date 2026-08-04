<?php

namespace App\Books\Application\UseCase;

use App\Books\Application\DTO\BookDTO;
use App\Books\Domain\Entity\Book;
use App\Books\Application\Mapper\BookMapper;
use App\Books\Domain\Repository\BookRepositoryInterface;
use App\Categories\Domain\Repository\CategoryRepositoryInterface;
use Symfony\Bundle\SecurityBundle\Security;

final class RecordBookByApi
{
    public function __construct(
        private BookMapper $bookMapper,
        private BookRepositoryInterface $bookRepository,
        private CategoryRepositoryInterface $categoryRepository,
        private Security $security,      
        
    ) {}

    public function execute(BookDTO $dto): Book
        {


            $user = $this->security->getUser();
            
                if (!$user) {
                        throw new \Exception('Utilisateur non authentifié');
                    }  

        
        // D'abord mapper le DTO
            $book = $this->bookMapper->toEntity($dto);

            // Ensuite définir l'auteur
            $book->setUser($user);

            // Définir le nom de l'auteur si nécessaire
            $book->setAuthorName($dto->authorName ?? $user->getUserIdentifier());

          
            // Dates
            $book->setCreatedAt(new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris')));

          

            foreach ($dto->categories as $categoryId) {
            $category = $this->categoryRepository->find($categoryId);

            if ($category) {
                $book->addCategory($category);
            }
        }

            $book->setCoverFile($dto->cover);

            if ($dto->cover) {
                $book->setCoverFile($dto->cover);
            }

            $book->setImages($dto->images ?? []);

           

            // Sauvegarder
            $this->bookRepository->save($book);

            return $book;
        }
}