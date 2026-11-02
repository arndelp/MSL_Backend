<?php

namespace App\SellerPayments\Infrastructure\Repository;

use App\SellerPayments\Domain\Entity\ClaimMessage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\SellerPayments\Domain\Repository\ClaimMessageRepositoryInterface;

/**
 * @extends ServiceEntityRepository<ClaimMessage>
 */
class ClaimMessageRepository extends ServiceEntityRepository implements ClaimMessageRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ClaimMessage::class);
    }

    public function findMessageByClaim(int $id): array
    {
        return $this->createQueryBuilder('cm')
            ->join('cm.claim', 'c')            
            ->where('c.id = :id')
            ->setParameter('id', $id)
            ->orderBy('cm.createdAt', 'ASC')
            ->getQuery()
            ->getResult(); //Pour avoir un tableau de valeur (message)
    }

    public function save(ClaimMessage $claimMessage): void
    {
        $em = $this->getEntityManager();
        $em->persist($claimMessage);
        $em->flush();

    }

    public function hasMessageNotReadByUser(int $userId): bool
    {
        return (bool) $this->createQueryBuilder('cm')
            ->join('cm.claim', 'c')            
            ->join('cm.author', 'a')
            ->where('(c.buyer = :id OR c.seller = :id)')
            ->andWhere('a.id != :id')
            ->andWhere('cm.isRead = false')
            ->setParameter('id', $userId)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult(); // pour avoir un retour booléen
    }

    public function hasMessageNotReadByUserAndClaim(int $userId, int $claimId): bool
    {
        return (bool) $this->createQueryBuilder('cm')
            ->join('cm.claim', 'c')            
            ->join('cm.author', 'a')
            ->where('(c.buyer = :userId OR c.seller = :userId)')
            ->andWhere('a.id != :userId')
            ->andWhere('cm.isRead = false')
            ->andWhere('c.id = :claimId')
            ->setParameter('userId', $userId)
            ->setParameter('claimId', $claimId)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findMessageByClaimAndNotUser(int $userId, int $claimId): array
    {
        return $this->createQueryBuilder('cm')
            ->join('cm.claim', 'c')
            ->join('cm.author', 'a')
            ->where('(c.buyer = :userId OR c.seller = :userId)')
            ->andWhere('a.id != :userId')
            ->andWhere('cm.isRead = false')
            ->andWhere('c.id = :claimId')
            ->setParameter('userId', $userId)
            ->setParameter('claimId', $claimId)
            ->getQuery()
            ->getResult();

    }


    // public function findMessageBySellerPayment(int $sellerPaymentId): array
    // {
    //     return $this->createQueryBuilder('cm')
    //         ->join('cm.claim', 'c')
    //         ->join('c.sellerPayment', 'sp')
    //         ->where('sp.id = :spId')
    //         ->setParameter('spId', $sellerPaymentId)
    //         ->orderBy('cm.createdAt', 'DESC')
    //         ->getQuery()
    //         ->getResult();
    // }
}
