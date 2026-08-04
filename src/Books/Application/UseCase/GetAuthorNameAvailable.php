<?php

namespace App\Books\Application\UseCase;

use App\Books\Domain\Repository\BookRepositoryInterface;

class GetAuthorNameAvailable
{
    public function __construct(
        private readonly BookRepositoryInterface $bookRepository
    ) {}

    public function execute(): array
    {
        return $this->bookRepository->findAllAuthorNames();
    }
}