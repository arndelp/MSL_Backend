<?php

namespace App\SellerPayments\Infrastructure\Repository;

use App\SellerPayments\Domain\Entity\Claim;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\SellerPayments\Domain\Repository\ClaimRepositoryInterface;

/**
 * @extends ServiceEntityRepository<Claim>
 */
class ClaimRepository extends ServiceEntityRepository implements ClaimRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Claim::class);
    }

    public function save(Claim $claim): void
    {
        $this->getEntityManager()->persist($claim);
        $this->getEntityManager()->flush();
    }

    public function findClaimBySP(int $sellerPaymentId): array
    {
        return $this->createQueryBuilder('c')
           ->join('c.sellerPayment', 'sp')
           ->where('sp.id = :spId')
           ->setParameter('spId', $sellerPaymentId)
           ->orderBy('c.createdAt', 'DESC')
           ->getQuery()
           ->getResult();
    }

    public function findById(int $claimId): ?Claim
        {
            return $this->find($claimId);
        }

}
