<?php

namespace App\Books\Domain\Repository;

use App\Books\Domain\Entity\Book;
use App\Users\Domain\Entity\User;
use App\Books\Application\DTO\BookFilterDTO;

interface BookRepositoryInterface
{
    public function save(Book $book): void;

    public function findAll(): array;

    public function findAvailable(int $page = 1, int $limit = 20): array;

    public function findPriceById(int $id): ?float;

    public function findTitleById(int $id): ?string;

    public function findById(int $id): ?Book;

    public function findBySeller(User $user): array;

    public function deleteBook(Book $book): void;

    public function findAllAuthorNames(): array;

    public function findAuthorNamesByUser(User $user): array;

    public function findNotVerified(): array;

    public function findDeletedBooks(): array;

    public function remove(Book $book): void;

    public function findByFilters(BookFilterDTO $dto, int $page, int $limit): array;

    public function findDistinctStatus(): array;

    public function findDistinctAuthorNames(): array;
    
    public function findAuthorNamesWithBookCount(): array;
}