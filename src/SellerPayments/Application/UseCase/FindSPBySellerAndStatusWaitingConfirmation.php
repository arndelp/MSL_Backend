<?php

namespace App\SellerPayments\Application\UseCase;

use App\SellerPayments\Domain\Repository\SellerPaymentRepositoryInterface;

final class FindSPBySellerAndStatusWaitingConfirmation
{
    public function __construct(
        private SellerPaymentRepositoryInterface $repository,
    ){}

    public function execute($seller): array
    {
        
        $items = $this->repository->findBySellerAndStatusWSC($seller);

        $data = array_map(fn($item) => [
            'id' => $item->getId(),
            //pour envoyer une collection
            'books' => $item->getOrder()->getOrderItems()
                ->filter(
                    fn($orderItem) => $orderItem->getSeller()?->getId() === $seller->getId()
                )
                ->map(fn($orderItem) => [
                    'title' => $orderItem->getBook()->getTitle(),
                    'quantity' => $orderItem->getQuantity(),
                    'format' => $orderItem->getBook()->getFormat()?->value,
                    'coverUrl' => $orderItem->getBook()->getCoverUrl(),
                ])
                ->toArray(),
            'shippingFirstname' => $item->getOrder()->getShippingFirstname(),
            'shippingLastname' => $item->getOrder()->getShippingLastname(),
            'shippingAddressLine1' => $item->getOrder()->getShippingAddressLine1(),
            'shippingAddressLine2' => $item->getOrder()?->getShippingAddressLine2(),
            'shippingPostalCode' => $item->getOrder()->getShippingPostalCode(),
            'shippingCity' => $item->getOrder()->getShippingCity(),
            'shippingCountry' => $item->getOrder()->getShippingCountry(),
            'createdAt' => $item->getCreatedAt()?->format('d-m-Y'),
            'confirmationToken' => $item->getConfirmationToken(),
            'paymentNumber' => $item->getPaymentNumber(),
            'shippingMethod' => $item->getShippingMethod(),
            

        ], $items);

        return $data;

        
    }
}

