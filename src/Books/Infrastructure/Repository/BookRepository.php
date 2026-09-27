<?php

namespace App\Books\Infrastructure\Repository;

use App\Books\Domain\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Books\Domain\Repository\BookRepositoryInterface;
use App\Users\Domain\Entity\User;
use App\Books\Application\DTO\BookFilterDTO;
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

    public function findAvailable(int $page = 1, int $limit = 20): array //On ne peut plus utiliser findBy car on a besoin de la pagination
    {
        $query = $this->createQueryBuilder('b')
            ->where('b.status = :status')
            ->andWhere('b.isVerified = :verified')
            ->setParameter('status', 'available')
            ->setParameter('verified', true);

        // Comptage total
        $countQb = clone $query;

        $total = (int) $countQb
            ->select('COUNT(b.id)')
            ->getQuery()
            ->getSingleScalarResult();

        // Pagination
        $books = $query
            ->orderBy('b.createdAt', 'DESC')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        return [
            'books' => $books,
            'total' => $total,
            'nbrePage' => (int) ceil($total / $limit),
            'currentPage' => $page,
        ];
    }

    public function findNotVerified(): array
    {
        return $this->findBy(
            ['status' => 'available','isVerified' => false],
            ['createdAt' => 'DESC']
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
                'isVerified' => true,
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

    public function findDeletedBooks(): array
    {
        return $this->findBy(
            ['status' => 'deleted'],
            ['title' => 'ASC']
        );
    }

    public function remove(Book $book): void
    {
        $em = $this->getEntityManager();
        $em->remove($book);
        $em->flush();
    }

    public function findByFilters(BookFilterDTO $dto, int $page, int $limit): array
    {
        $query = $this->createQueryBuilder('m');

        if ($dto->status) {
            $query->andWhere('m.status = :status')
                ->setParameter('status', $dto->status);
        }

        if ($dto->authorName) {
            $query->andWhere('m.authorName = :authorName')
                ->setParameter('authorName', $dto->authorName);
        }

        if ($dto->search) {
            $query->andWhere(
                'm.title LIKE :search OR m.authorName LIKE :search'
            )
            ->setParameter('search', '%' . $dto->search . '%');
        }

        if ($dto->category) {
            $query->join('m.categories', 'c')
                ->andWhere('c.id = :category')
                ->setParameter('category', $dto->category);
        }

        if ($dto->isVerified !== null) {
            $query->andWhere('m.isVerified = :verified')
                ->setParameter('verified', $dto->isVerified);
        }


        // Comptage total avant pagination
        $countQb = clone $query;

        $total = (int) $countQb
            ->select('COUNT(DISTINCT m.id)')
            ->getQuery()
            ->getSingleScalarResult();

        // Pagination
        $query
            ->orderBy('m.title', 'ASC')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        $books = $query
            ->getQuery()
            ->getResult();

        return [
            'books' => $books,
            'total' => $total,
            'nbrePage' => (int) ceil($total / $limit),
            'currentPage' => $page
        ];
    }

    //récupération des statuss pour filtre dynamique
    public function findDistinctStatus(): array
    {
        $qb = $this->createQueryBuilder('c') 
            ->select('DISTINCT c.status')
            ->orderBy('c.status', 'ASC');

        $results = $qb->getQuery()->getArrayResult();

        return array_map(fn($item) => $item['status'], $results);
    }

    public function findDistinctAuthorNames(): array
    {
        $qb = $this->createQueryBuilder('c')
           ->select('DISTINCT c.authorName')
           ->orderBy('c.authorName', 'ASC');

        $results = $qb->getQuery()->getArrayResult();

        return array_map(fn($item) => $item['authorName'], $results);
    }   
    

    public function findAuthorNamesWithBookCount(): array
    {
        return $this->createQueryBuilder('b')
            ->select('b.authorName AS authorName')
            ->addSelect('COUNT(b.id) AS count')
            ->where('b.status = :status')
            ->andWhere('b.isVerified = :verified')
            ->andWhere('b.authorName IS NOT NULL')
            ->setParameter('status', 'available')
            ->setParameter('verified', true)
            ->groupBy('b.authorName')
            ->orderBy('b.authorName', 'ASC')
            ->getQuery()
            ->getArrayResult();
    }


}
