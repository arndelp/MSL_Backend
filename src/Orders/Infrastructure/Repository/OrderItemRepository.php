<?php

namespace App\Orders\Infrastructure\Repository;

use App\Orders\Domain\Entity\OrderItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Orders\Domain\Repository\OrderItemRepositoryInterface;
use App\Users\Domain\Entity\User;
use App\Enum\OrderItemStatus;
use Symfony\Component\HttpFoundation\Response;


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

     public function findById(int $id): ?OrderItem
    {
        return $this->find($id);
    }
}
