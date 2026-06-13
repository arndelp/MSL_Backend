<?php

namespace App\Orders\Domain\Repository;

use App\Orders\Domain\Entity\OrderItem;


interface OrderItemRepositoryInterface
{
    public function addOrderItem(OrderItem $orderItem): void;

   

}