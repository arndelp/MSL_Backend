<?php

namespace App\Orders\Infrastructure\Repository;

use App\Orders\Domain\Entity\OrderItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Orders\Domain\Repository\OrderItemRepositoryInterface;

/**
 * @extends ServiceEntityRepository<OrderItem>
 */
class OrderItemRepository extends ServiceEntityRepository implements OrderItemRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrderItem::class);
    }

    public function addOrderItem(OrderItem $orderItem): void
    {
        $em = $this->getEntityManager();
        $em->persist($orderItem);
        $em->flush();
    }

    public function findByStripeSessionId(string $stripeSessionId): array
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.stripeSessionId = :stripeSessionId')
            ->setParameter('stripeSessionId', $stripeSessionId)
            ->getQuery()
            ->getResult();
    }

    public function save(OrderItem $orderItem): void
    {
        $em = $this->getEntityManager();
        $em->persist($orderItem);
        $em->flush();
    }

   public function saveAll(array $items): void
    {
        $em = $this->getEntityManager();

        foreach ($items as $item) {
            $em->persist($item);
        }

        $em->flush();
    }
}
