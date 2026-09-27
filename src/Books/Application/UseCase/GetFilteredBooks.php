<?php
namespace App\Books\Application\UseCase;

use App\Books\Application\DTO\BookFilterDTO;
use App\Books\Domain\Repository\BookRepositoryInterface;

class GetFilteredBooks
{
    public function __construct(private BookRepositoryInterface $BookRepository) {}
    //Reçoit un DTO de filtre et délègue au repository
    public function execute(BookFilterDTO $filter, int $page, int $limit): array
    {
        return $this->BookRepository->findByFilters($filter, $page, $limit);
    }
}
