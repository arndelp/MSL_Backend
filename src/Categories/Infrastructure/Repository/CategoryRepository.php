<?php

namespace App\Categories\Infrastructure\Repository;

use App\Categories\Domain\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Categories\Domain\Repository\CategoryRepositoryInterface;

/**
 * @extends ServiceEntityRepository<Category>
 */
class CategoryRepository extends ServiceEntityRepository implements CategoryRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }


    public function findAll(): array
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.id', 'ASC')
            ->getQuery()
            ->getResult();
    }   
 

    public function save(Category $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByIds(array $ids): array 
    {
        return $this->createQueryBuilder('c')   // 'c' est l'alias pour la table Category
            ->where('c.id IN (:ids)')           // Filtre les catégories dont l'ID est dans le tableau $ids
            ->setParameter('ids', $ids)         // Associe le paramètre :ids au tableau $ids
            ->getQuery()                        // Exécute la requête
            ->getResult();                      // Retourne un tableau d'objets Category correspondant aux IDs fournis
    }



    public function findAllCategoriesWithBookCount(): array
    {
        return $this->createQueryBuilder('c')

            // Ce que je veux récupérer
            ->select('c.id')
            ->addSelect('c.name')
            ->addSelect('parent.id AS parentId')
            ->addSelect('COUNT(b.id) AS bookCount')

            // Récupérer le parent de la catégorie
            ->leftJoin('c.parent', 'parent')

            // Récupérer les livres de la catégorie
            ->leftJoin(
                'c.books',
                'b',
                'WITH',
                'b.status = :status AND b.isVerified = :verified'
            )

            ->setParameter('status', 'available')
            ->setParameter('verified', true)

            // Comme on utilise COUNT(), on groupe
            ->groupBy('c.id')
            ->addGroupBy('c.name')
            ->addGroupBy('parent.id')

            ->orderBy('c.position', 'ASC')
            ->addOrderBy('c.name', 'ASC')
            

            ->getQuery()
            ->getArrayResult();
    }



    //    /**
    //     * @return Category[] Returns an array of Category objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Category
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
