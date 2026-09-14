<?php

namespace App\Orders\Infrastructure\Repository;

use App\Orders\Domain\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Orders\Domain\Repository\OrderRepositoryInterface;

/**
 * @extends ServiceEntityRepository<Order>
 */
class OrderRepository extends ServiceEntityRepository implements OrderRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    public function save(Order $order): void
    {
        $em = $this->getEntityManager();
        $em->persist($order);
        $em->flush();
    }

    public function findById(int $id): ?Order
    {
        return $this->find($id);
    }

    public function remove(Order $order): void
    {
        $em = $this->getEntityManager();
        $em->remove($order);
        $em->flush();
    }

    public function findByStripeSessionId(string $stripeSessionId): ?Order
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.stripe_session_id = :sessionId')
            ->setParameter('sessionId', $stripeSessionId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findPaginated(int $page,int $nbre): array
    {
        $query = $this -> createQueryBuilder('m');

        $countQb = clone $query;               // on clone $query pour séparer le comptage des marker et la récupération des résultats
        $total = (int) $countQb ->select('COUNT(m.id)')
                                ->getQuery()
                                ->getSingleScalarResult(); 
        
        // ----------- Pagination -----------
        $query  ->orderBy('m.id', 'DESC')
                ->setFirstResult(($page - 1) * $nbre)
                ->setMaxResults($nbre);

        $orders = $query->getQuery()->getResult() ?? []; //si pas de résultat on retourne un tableau vide

        return [
            'orders'   => $orders,
            'total'     => $total,
            'nbrePage'  => (int) ceil($total / $nbre),
            'currentPage' => $page
        ];
    }

    public function countAll(): int
    {
        return $this->count([]);
    }

   
   
}
