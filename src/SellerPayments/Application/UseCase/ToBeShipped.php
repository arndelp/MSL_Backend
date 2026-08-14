<?php
namespace App\SellerPayments\Application\UseCase;

use App\SellerPayments\Domain\Repository\SellerPaymentRepositoryInterface;
use App\Enum\SellerPaymentStatus;

class ToBeShipped
{
    public function __construct(private SellerPaymentRepositoryInterface $sellerPaymentRepository) {}

    public function execute(int $id): void
    {
        $orderItem = $this->sellerPaymentRepository->findById($id);

        if (!$orderItem) {
            throw new \InvalidArgumentException('Élément de commande introuvable');
        }
       
        $orderItem->setStatus(SellerPaymentStatus::SHIPPED);

        $orderItem->setShippedAt(new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris')));

        $orderItem->setUpdatedAt(new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris')));

        $this->sellerPaymentRepository->save($orderItem);
    }
}   