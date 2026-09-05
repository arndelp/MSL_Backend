<?php

namespace App\BackUsers\Infrastructure\Repository;

use App\BackUsers\Domain\Entity\BackUser;
use App\BackUsers\Domain\Repository\BackUserRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<BackUser>
 */
class BackUserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BackUser::class);
    }

     public function findById(int $id): ?BackUser
    {
        return $this->find($id);
    }

    public function findOneByEmail(string $email): ?BackUser
    {
        return $this->findOneBy(['email' => $email]);
    }

    public function save(BackUser $backUser): void
    {
        $em = $this->getEntityManager(); //hérité de ServiceEntiyRepository 
        $em->persist($backUser);
        $em->flush();
    }

    public function remove(BackUser $backUser): void
    {
        $em = $this->getEntityManager(); 
        $em->remove($backUser);
        $em->flush();
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $backUser, string $newHashedPassword): void
    {
        if (!$backUser instanceof BackUser) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $backUser::class));
        }

        $backUser->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($backUser);
        $this->getEntityManager()->flush();
    }

//    /**
//     * @return BackUser[] Returns an array of BackUser objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('b.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?BackUser
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
