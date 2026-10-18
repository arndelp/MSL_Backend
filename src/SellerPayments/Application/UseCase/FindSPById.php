<?php

namespace App\SellerPayments\Application\UseCase;

use App\SellerPayments\Domain\Repository\SellerPaymentRepositoryInterface;


final class FindSPById
{
    public function __construct(
        private SellerPaymentRepositoryInterface $repository,
       
      
    ){}

    public function execute($id): ?array
    {
        $SP = $this->repository->findById($id);

        $data = [
            'id' => $SP->getId(),
            'orderItems' => $SP->getOrderItems()
                ->map(fn($orderItem) => [
                    'id' => $orderItem->getId(),
                    'title' => $orderItem->getBookTitle()
                        ?? $orderItem->getBook()?->getTitle(),
                ])
                ->toArray(),
            ];

        return $data;
    }
}