<?php

namespace App\Orders\Domain\Repository;

use App\Orders\Domain\Entity\OrderItem;
use App\Users\Domain\Entity\User;


interface OrderItemRepositoryInterface
{
    public function addOrderItem(OrderItem $orderItem): void;  

    public function save(OrderItem $orderItem): void;

    public function saveAll(array $items): void;

    public function findById(int $id): ?OrderItem;

    
   }
