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

    public function findMessageByClaim(int $claimId): array
    {
        return $this->createQueryBuilder('cm')
            ->join('cm.claim', 'c')
            ->where('c.id = :claimId')
            ->setParameter('claimId', $claimId)
            ->orderBy('cm.createdAt', 'DESC')
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
