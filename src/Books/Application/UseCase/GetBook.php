<?php

namespace App\Books\Application\UseCase;

use App\Books\Domain\Entity\Book;
use App\Books\Domain\Repository\BookRepositoryInterface;

// uniquement une récupération d'un livre par Id => pas de DTO

class GetBook
{
    public function __construct(private BookRepositoryInterface $bookRepository) {}

    public function execute(int $id): ?Book
    {
    $book = $this->bookRepository->findById($id);
        
    return $book;    
    }
}