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
}
