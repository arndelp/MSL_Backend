<?php

namespace App\SellerPayments\Application\UseCase;


use App\SellerPayments\Domain\Repository\SellerPaymentRepositoryInterface;

final class FindSPByBuyerAndStatusDelivered
{
    public function __construct(
        private SellerPaymentRepositoryInterface $repository,
    ){}

    public function execute($buyer):  array
    {
       
        $items = $this->repository->findByBuyerAndStatusDelivered($buyer);

      

        $data = array_map(fn($item) => [
            'id' => $item->getId(),
            //pour envoyer une collection
            'books' => $item->getOrderItems()
                ->map(fn($orderItem) => [
                    'orderItemId' => $orderItem->getId(),
                    'title' => $orderItem->getBookTitle()
                        ?? $orderItem->getBook()?->getTitle(),
                    'quantity' => $orderItem->getQuantity(),
                    'format' => $orderItem->getBook()?->getFormat()?->value,
                    'coverUrl' => $orderItem->getBook()?->getCoverUrl(),
                ])
                ->toArray(),            
            'createdAt' => $item->getCreatedAt()?->format('d-m-Y'),            
            'paymentNumber' => $item->getPaymentNumber(),            
            'deliveredAt' => $item->getDeliveredAt()?->format('d-m-Y'),
            'coolingOffPeriod' => $item->getEndCoolingOffPeriodAt()?->format('Y-m-d') //ATTENTION: format Y m D
        ], $items);        

        return $data;
    }
}