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
    $book->setAuthor($user);

    // Définir le nom de l'auteur si nécessaire
    $book->setAuthorName($dto->authorName ?? $user->getUserIdentifier());

    // Dates
    $book->setCreatedAt(new \DateTimeImmutable());
    $book->setUpdatedAt(new \DateTimeImmutable());

    // Ajouter les catégories
    $categories = $this->categoryRepository->findByIds($dto->categories);
    foreach ($categories as $category) {
        $book->addCategory($category);
    }

    // Sauvegarder
    $this->bookRepository->save($book);

  



    return $book;
}
}