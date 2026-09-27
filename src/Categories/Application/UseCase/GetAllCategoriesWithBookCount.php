<?php

namespace App\Categories\Application\UseCase;

use App\Categories\Domain\Repository\CategoryRepositoryInterface;

class GetAllCategoriesWithBookCount
{
    public function __construct(
        private readonly CategoryRepositoryInterface $categoryRepository
    ) {}

    public function execute(): array
    {
        return $this->categoryRepository->findAllCategoriesWithBookCount();
    }
}
