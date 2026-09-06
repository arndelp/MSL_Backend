<?php

namespace App\Books\Application\UseCase;

use App\Books\Domain\Repository\BookRepositoryInterface;

class GetNotVerifiedBooks
{
    public function __construct(
        private readonly BookRepositoryInterface $bookRepository
    ) {}

    public function execute(): array
    {
        $books = $this->bookRepository->findNotVerified();

        return array_map(fn ($book) => [
            'id' => $book->getId(),
            'title' => $book->getTitle(),
            'authorName' => $book->getAuthorName(),
            'price' => $book->getPrice(),
            'format' => $book->getFormat()?->value,           
            'createdAt' => $book->getCreatedAt()?->format('Y-m-d H:i:s'),           
            'cover' => $book->getCover(),          
            
        ], $books);
    }
}