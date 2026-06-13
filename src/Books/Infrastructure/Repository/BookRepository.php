<?php

namespace App\Books\Infrastructure\Repository;

use App\Books\Domain\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Books\Domain\Repository\BookRepositoryInterface;
/**
 * @extends ServiceEntityRepository<Book>
 */
class BookRepository extends ServiceEntityRepository implements BookRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    public function save(Book $book): void
    {
        $em = $this->getEntityManager();
        $em->persist($book);
        $em->flush();
        
    }

    public function findAll(): array
    {
        return $this->findBy([], ['title' => 'ASC']);
    }

    public function findPriceById(int $id): ?float
    {
        $book = $this->find($id);
        return $book ? $book->getPrice() : null;
    }

    public function findTitleById(int $id): ?string
    {
        $book = $this->find($id);
        return $book ? $book->getTitle() : null;
    }

    public function findById(int $id): ?Book
    {
        return $this->find($id);
    }
}
