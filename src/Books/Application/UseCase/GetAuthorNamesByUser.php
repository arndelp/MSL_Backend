<?php

namespace App\Books\Application\UseCase;

use App\Books\Domain\Repository\BookRepositoryInterface;
use App\Users\Domain\Entity\User;

final class GetAuthorNamesByUser
{
    public function __construct(
        private BookRepositoryInterface $bookRepository
    ) {}

    public function execute(User $user): array
    {
        return $this->bookRepository->findAuthorNamesByUser($user);
    }
}