<?php

namespace App\Books\Domain\Repository;

use App\Books\Domain\Entity\Book;

interface BookRepositoryInterface
{
    public function save(Book $book): void;

    public function findAll(): array;

    public function findPriceById(int $id): ?float;

    public function findTitleById(int $id): ?string;
    
}