<?php

namespace App\Books\Application\UseCase;

use App\Books\Domain\Repository\BookRepositoryInterface;
use Throwable;

class GetAllBooks
{
    public function __construct(
        private readonly BookRepositoryInterface $bookRepository
    ) {}

    public function execute(): array
    {
        try {
            $books = $this->bookRepository->findAvailable();

            return array_map(fn ($book) => [
                'id' => $book->getId(),
                'title' => $book->getTitle(),
                'authorName' => $book->getAuthorName(),
                'price' => $book->getPrice(),                
                'format' => $book->getFormat()?->value,
                'coverUrl' => $book->getCoverUrl(),
                'categories' => array_map(
                    fn ($cat) => [
                        'id' => $cat->getId(),
                        'name' => $cat->getName(),
                    ],
                    $book->getCategories()->toArray()
                ),               
                'quantityAvailable' => $book->getQuantityAvailable(),

            ], $books);
        } catch (Throwable $e) {
            // Log the error or handle it as needed
            throw new \RuntimeException('Aucun livre trouvé: ' . $e->getMessage());   
        }
    }
}