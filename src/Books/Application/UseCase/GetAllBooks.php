<?php

namespace App\Books\Application\UseCase;

use App\Books\Application\DTO\BookFilterDTO;
use App\Books\Domain\Repository\BookRepositoryInterface;
use Throwable;

class GetAllBooks
{
    public function __construct(
        private readonly BookRepositoryInterface $bookRepository
    ) {}

    public function execute(BookFilterDTO $filter, int $page = 1, int $limit): array
    {
        try {
            $result = $this->bookRepository->findByFilters(
                $filter,
                $page,
                $limit,                             
            );

            $books = array_map(fn ($book) => [
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
            ], $result['books']);

            return [
                'books' => $books,
                'total' => $result['total'],
                'nbrePage' => $result['nbrePage'],
                'currentPage' => $result['currentPage'],
            ];

        } catch (Throwable $e) {
            throw new \RuntimeException(
                'Aucun livre trouvé: ' . $e->getMessage()
            );
        }
    }
}