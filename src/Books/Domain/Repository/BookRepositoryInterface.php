<?php

namespace App\Books\Domain\Repository;

use App\Books\Domain\Entity\Book;
use App\Users\Domain\Entity\User;

interface BookRepositoryInterface
{
    public function save(Book $book): void;

    public function findAll(): array;

    public function findAvailable(): array;

    public function findPriceById(int $id): ?float;

    public function findTitleById(int $id): ?string;

    public function findById(int $id): ?Book;

    public function findBySeller(User $user): array;

    public function deleteBook(Book $book): void;
    
}