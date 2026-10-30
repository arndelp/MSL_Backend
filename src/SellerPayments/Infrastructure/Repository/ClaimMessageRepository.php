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
            ->getResult();
    }

    public function save(ClaimMessage $claimMessage): void
    {
        $em = $this->getEntityManager();
        $em->persist($claimMessage);
        $em->flush();

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
