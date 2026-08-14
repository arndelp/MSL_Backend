<?php

namespace App\SellerPayments\Domain\Repository;

use App\SellerPayments\Domain\Entity\SellerPayment;
use App\Users\Domain\Entity\User;
use App\Orders\Domain\Entity\Order;

interface SellerPaymentRepositoryInterface 
{
    public function findBySellerAndStatusWSC(User $seller): array;

    public function findBySellerAndStatusConfirmed(User $seller): array;

    public function findBySellerAndStatusShipped(User $seller): array;

    public function findById(int $id): ?SellerPayment;

    public function findConfirmationTokenById(int $id, string $confirmationToken): ?SellerPayment;

    public function save(SellerPayment  $sellerPayment): void;

     public function hasPendingSellerPayments(Order $order): bool;
}