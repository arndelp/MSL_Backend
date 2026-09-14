<?php
namespace App\Books\Application\UseCase;

use App\Books\Domain\Repository\BookRepositoryInterface;

class RejectBook
{
    public function __construct(private BookRepositoryInterface $bookRepository) {}

    public function execute(int $id): void
    {
        $book = $this->bookRepository->findById($id);

        if (!$book) {
            throw new \InvalidArgumentException('Livre introuvable');
        }
       
        $book->setStatus('deleted');

        $this->bookRepository->save($book);
    }
}
