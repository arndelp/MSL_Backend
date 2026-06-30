<?php

namespace App\Books\Application\UseCase;

use App\Books\Domain\Repository\BookRepositoryInterface;


class GetPersonnalBooksByApi
{
    public function __construct(
        private readonly BookRepositoryInterface $bookRepository
    ) {}

    public function execute($user): array
    {
      

    $books = $this->bookRepository->findBySeller($user);

    $data = array_map(fn ($book) => [
        'id' => $book->getId(),
        'title' => $book->getTitle(),
        'authorName' => $book->getAuthorName(),
        'price' => $book->getPrice(),
        'quantity' => $book->getQuantity(),
        'format' => $book->getFormat()?->value,
        'coverUrl' => $book->getCoverUrl(),
    ], $books);

    return $data;
    }
}