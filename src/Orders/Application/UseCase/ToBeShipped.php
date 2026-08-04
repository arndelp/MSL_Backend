<?php
namespace App\Orders\Application\UseCase;

use App\Orders\Domain\Repository\OrderItemRepositoryInterface;
use App\Enum\OrderItemStatus;

class ToBeShipped
{
    public function __construct(private OrderItemRepositoryInterface $orderItemRepository) {}

    public function execute(int $id): void
    {
        $orderItem = $this->orderItemRepository->findById($id);

        if (!$orderItem) {
            throw new \InvalidArgumentException('Élément de commande introuvable');
        }
       
        $orderItem->setStatus(OrderItemStatus::SHIPPED);

        $orderItem->setShippedAt(new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris')));

        $orderItem->setUpdatedAt(new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris')));

        $this->orderItemRepository->save($orderItem);
    }
}   