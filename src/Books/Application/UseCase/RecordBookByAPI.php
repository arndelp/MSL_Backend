<?php

namespace App\Books\Application\UseCase;

use App\Books\Application\DTO\BookDTO;
use App\Books\Application\Mapper\BookMapper;
use App\Books\Domain\Repository\BookRepositoryInterface;
use App\Categories\Domain\Repository\CategoryRepositoryInterface;


final class RecordBookByAPI
{
    public function __construct(
        private readonly BookMapper $bookMapper,
        private readonly BookRepositoryInterface $bookRepository,
        private readonly CategoryRepositoryInterface $categoryRepository
    ) {}

    public function execute(BookDTO $bookDTO): void
    {
        $book = $this->bookMapper->toEntity($bookDTO);

        foreach ($bookDTO->categories as $categoryId) {
            $category = $this->categoryRepository->find($categoryId);
            if ($category) {
                $book->addCategory($category);
            }
        }

        $this->bookRepository->save($book, true);
    }
}