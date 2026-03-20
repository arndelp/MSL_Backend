<?php

namespace App\Categories\Domain\Repository;

use App\Categories\Domain\Entity\Category;

interface CategoryRepositoryInterface
{
    /**
     * @return Category[] Returns an array of Category objects
     */
    public function findAll(): array;
    

    public function save(Category $entity, bool $flush = false): void;
}
