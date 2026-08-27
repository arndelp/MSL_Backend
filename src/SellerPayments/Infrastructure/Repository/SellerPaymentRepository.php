<?php

namespace App\SellerPayments\Infrastructure\Repository;

use App\SellerPayments\Domain\Entity\SellerPayment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\SellerPayments\Domain\Repository\SellerPaymentRepositoryInterface;
use App\Users\Domain\Entity\User;
use App\Enum\SellerPaymentStatus;
use App\Orders\Domain\Entity\Order;

/**
 * @extends ServiceEntityRepository<SellerPayment>
 */
class SellerPaymentRepository extends  ServiceEntityRepository implements SellerPaymentRepositoryInterface    
{
    private ?DateTimeImmutable $confirmation_token_expires_at;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SellerPayment::class);
       
    }

    public function findBySellerAndStatusWSC(User $seller): array
    {
        return
       
            $this->findBy(
                [
                    'seller' => $seller,
                    'status' => SellerPaymentStatus::WAITING_SELLER_CONFIRMATION
                ],
                [
                    'created_at' => 'DESC'
                ]
            );
    }

    public function findBySellerAndStatusConfirmed(User $seller): array
    {
        return
            $this->findBy(
                [
                    'seller' => $seller,
                    'status' => SellerPaymentStatus::CONFIRMED
                ],
                [
                    'updated_at' => 'DESC'
                ]
            );
    }

    public function findBySellerAndStatusShipped(User $seller): array
    {
        return
            $this->findBy(
                [
                    'seller' => $seller,
                    'status' => SellerPaymentStatus::SHIPPED
                ],
                [
                    'updated_at' => 'DESC'
                ]
            );
    }

    public function findById(int $id): ?SellerPayment
    {
        return $this->find($id);
    }

    public function findConfirmationTokenById(int $id, string $confirmationToken): ?SellerPayment   
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

    public function save(SellerPayment  $sellerPayment): void
    {
        $em = $this->getEntityManager();
        $em->persist($sellerPayment); //persist() signifie : prend cette entité en charge
        $em->flush(); //flush() signifie : écrit les données en base de donnée
    }

    public function hasPendingSellerPayments(Order $order): bool
    {
        return (int) $this->createQueryBuilder('sp') //sp:alias pour sellerPayment (=FROM sellerPayment)
            ->select('COUNT(sp.id)') //Compte moi combien de sellerPayments correspondent à mes critère
            ->andWhere('sp.order = :order') //je garde les seller payment correspondant à l'order (doctrine utilise la clé primaire pour la requête)
            ->andWhere('sp.status = :status') //et le status que je veux
            ->setParameter('order', $order) //valeur correspondant à order
            ->setParameter( //valeur correspondant à statut
                'status',
                SellerPaymentStatus::WAITING_SELLER_CONFIRMATION
            )
            ->getQuery() //transforme le queryBuilder en objet Query prêt à être exécuter
            ->getSingleScalarResult() > 0; //récupère la valeur, si > 0 = true sinon false
    }

    public function findExpiredSellerPayments(
        \DateTimeImmutable $now
    ): array {
        return $this->createQueryBuilder('sp')
            ->andWhere('sp.status = :status')
            ->andWhere('sp.confirmation_token_expires_at IS NOT NULL')
            ->andWhere('sp.confirmation_token_expires_at < :now')
            ->setParameter(
                'status',
                SellerPaymentStatus::WAITING_SELLER_CONFIRMATION
            )
            ->setParameter('now', $now)
            ->getQuery()
            ->getResult();
    }
}



