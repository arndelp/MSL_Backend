<?php
namespace App\Categories\Application\UseCase;

use App\Categories\Domain\Repository\CategoryRepositoryInterface;


class GetCategory
{
    private CategoryRepositoryInterface $categoryRepository;

    public function __construct(CategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function execute(): array
    {
        $category = $this->categoryRepository->findAll();

        return $category;
    }
}