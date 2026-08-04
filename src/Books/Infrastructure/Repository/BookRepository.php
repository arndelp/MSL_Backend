<?php

namespace App\Books\Infrastructure\Repository;

use App\Books\Domain\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Books\Domain\Repository\BookRepositoryInterface;
use App\Users\Domain\Entity\User;
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

    public function findAvailable(): array
    {
        return $this->findBy(
            ['status' => 'available'],
            ['title' => 'ASC']
        );
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

    public function findBySeller(User $user): array
    {
        return $this->findBy(
            [
                'user' => $user,
                'status' => 'available',
            ],
            ['title' => 'ASC'],
        );
    }

    public function deleteBook(Book $book): void
    {
        

        $em = $this->getEntityManager();
        $em->remove($book);
        $em->flush();
    }

    //Récupérer tout les nom d'auteur de livre disponible dans l'ordre alphabétique
    public function findAllAuthorNames(): array
    {
        $qb = $this->createQueryBuilder('b')
            ->select('DISTINCT b.authorName')
            ->where('b.status = :status')
            ->setParameter('status', 'available')
            ->orderBy('b.authorName', 'ASC');

        return $qb->getQuery()->getResult();
    }

    public function findAuthorNamesByUser(User $user): array
    {
        $qb = $this->createQueryBuilder('b')
            ->select('DISTINCT b.authorName')
            ->where('b.user = :user')            
            ->setParameter('user', $user)
            ->orderBy('b.authorName', 'ASC');

        return $qb->getQuery()->getResult();
    }
}
