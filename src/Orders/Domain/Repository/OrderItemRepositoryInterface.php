<?php

namespace App\Orders\Domain\Repository;

use App\Orders\Domain\Entity\OrderItem;
use App\Users\Domain\Entity\User;


interface OrderItemRepositoryInterface
{
    public function addOrderItem(OrderItem $orderItem): void;

    public function findByStripeSessionId(string $stripeSessionId): array;

    public function save(OrderItem $orderItem): void;

    public function saveAll(array $items): void;

   public function findByStatusAndSellerPendingConfirmation(User $seller):array;

    public function findByStatusAndSellerConfirmed(User $seller):array;

    public function findByStatusAndSellerDelivered(User $seller):array;

    public function findByStatusAndSellerShipped(User $seller):array;

    public function findById(int $id): ?OrderItem;

    public function findConfirmationTokenById(int $id, string $confirmationToken): ?OrderItem;
   }
