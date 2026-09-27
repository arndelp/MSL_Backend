<?php

namespace App\SellerPayments\Application\UseCase;

use App\SellerPayments\Domain\Entity\SellerPayment;
use App\SellerPayments\Domain\Repository\SellerPaymentRepositoryInterface;

class GetSellerPayment
{
    public function __construct(private SellerPaymentRepositoryInterface $sellerPaymentRepository) {}

    public function execute(int $id): ?SellerPayment
    { 
        $sellerPayment = $this->sellerPaymentRepository->findById($id);

        return $sellerPayment;    
    }
}