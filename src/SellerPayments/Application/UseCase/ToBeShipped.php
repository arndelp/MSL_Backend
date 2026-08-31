<?php
namespace App\SellerPayments\Application\UseCase;

use App\SellerPayments\Domain\Repository\SellerPaymentRepositoryInterface;
use App\Enum\SellerPaymentStatus;

class ToBeShipped
{
    public function __construct(private SellerPaymentRepositoryInterface $sellerPaymentRepository) {}

    public function execute(int $id): void
    {
        $SP = $this->sellerPaymentRepository->findById($id);

        if (!$SP) {
            throw new \InvalidArgumentException('Élément de commande introuvable');
        }
       
        $SP->setStatus(SellerPaymentStatus::SHIPPED);

        $SP->setShippedAt(new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris')));

        $SP->setUpdatedAt(new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris')));

        $this->sellerPaymentRepository->save($SP);
    }
}   