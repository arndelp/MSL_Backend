<?php

namespace App\Books\Application\UseCase;

use App\Books\Domain\Repository\BookRepositoryInterface;
use App\Books\Domain\Entity;

class GetAuthorNamesWithBookCount
{
    public function __construct(
        private readonly BookRepositoryInterface $bookRepository
    ) {}

    public function execute(): array
    {
        return $this->bookRepository->findAuthorNamesWithBookCount();
    }
}


