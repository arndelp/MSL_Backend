<?php
namespace App\Books\Application\UseCase;

use App\Books\Domain\Repository\BookRepositoryInterface;


// Rassemble les deux  findDistinctDays et findDistinctSchedule
class GetDistinctFilterValues
{
    public function __construct(private BookRepositoryInterface $bookRepository) {}

    public function execute(): array
    {
        return [
            'status' => $this->bookRepository->findDistinctStatus(),
            'authorName' => $this->bookRepository->findDistinctAuthorNames(),
            
        ];
    }
}
