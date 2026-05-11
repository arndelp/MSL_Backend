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
            return $this->bookRepository->findAll();
        } catch (Throwable $e) {
            // Log the error or handle it as needed
            throw new \RuntimeException('Aucun livre trouvé: ' . $e->getMessage());   
        }
    }
}