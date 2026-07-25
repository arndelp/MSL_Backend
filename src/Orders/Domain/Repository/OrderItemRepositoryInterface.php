<?php

namespace App\Orders\Domain\Repository;

use App\Orders\Domain\Entity\OrderItem;


interface OrderItemRepositoryInterface
{
    public function addOrderItem(OrderItem $orderItem): void;

    public function findByStripeSessionId(string $stripeSessionId): array;

    public function save(OrderItem $orderItem): void;

    public function saveAll(array $items): void;

   
}
