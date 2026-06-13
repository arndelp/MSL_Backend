<?php

namespace App\Orders\Domain\Repository;

use App\Orders\Domain\Entity\Order;

interface OrderRepositoryInterface
{
    public function save(Order $order): void;

    public function findById(int $id): ?Order;

    public function remove(Order $order): void;
}