<?php

namespace App\Books\Domain\Repository;

use App\Books\Domain\Entity\Book;

interface BookRepositoryInterface
{
    public function save(Book $book): void;
}