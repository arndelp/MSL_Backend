<?php
namespace App\Books\Application\UseCase;

use App\Books\Domain\Repository\BookRepositoryInterface;

class ToBeUnavailable
{
    public function __construct(private BookRepositoryInterface $bookRepository) {}

    public function execute(int $id): void
    {
        $book = $this->bookRepository->findById($id);

        if (!$book) {
            throw new \InvalidArgumentException('Livre introuvable');
        }
       
        $book->setQuantity(0);

        $this->bookRepository->save($book);
    }
}
