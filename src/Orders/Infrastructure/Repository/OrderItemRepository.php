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

    public function findByStripeSessionId(string $stripeSessionId): array
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.stripeSessionId = :stripeSessionId')
            ->setParameter('stripeSessionId', $stripeSessionId)
            ->getQuery()
            ->getResult();
    }

    public function findByStatusAndSellerPendingConfirmation(User $seller):array
    {
        return  
            $this->findBy(
            [
                'user' => $seller,
                'status' => OrderItemStatus::PENDING_AUTHOR_CONFIRMATION,
            ],
            [
                'created_at' => 'DESC'
            ]
            
        );
    }

    public function findByStatusAndSellerConfirmed(User $seller):array
    {
        return  
            $this->findBy(
            [
                'user' => $seller,
                'status' => OrderItemStatus::CONFIRMED,
            ],            
            [
                'updated_at' => 'DESC'
            ]
            
        );
    }

    public function findByStatusAndSellerDelivered(User $seller):array
    {
        return  
            $this->findBy(
            [
                'user' => $seller,
                'status' => OrderItemStatus::DELIVERED,
            ]
            
        );
    }

    public function findByStatusAndSellerShipped(User $seller):array
    {
        return  
            $this->findBy(
            [
                'user' => $seller,
                'status' => OrderItemStatus::SHIPPED,
            ]
            
        );
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

    public function findConfirmationTokenById(int $id, string $confirmationToken): ?OrderItem
    {
        return $this->createQueryBuilder('oi')
            ->andWhere('oi.id = :id')
            ->andWhere('oi.confirmation_token = :confirmationToken')
            ->andWhere('oi.confirmation_token_expires_at > :now')
            ->setParameter('id', $id)
            ->setParameter('confirmationToken', $confirmationToken)
            ->setParameter('now', new \DateTimeImmutable())
            ->getQuery()
            ->getOneOrNullResult();
    }


}
