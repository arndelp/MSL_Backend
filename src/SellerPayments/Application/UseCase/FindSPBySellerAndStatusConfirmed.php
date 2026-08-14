<?php

namespace App\SellerPayments\Application\UseCase;


use App\SellerPayments\Domain\Repository\SellerPaymentRepositoryInterface;

final class FindSPBySellerAndStatusConfirmed
{
    public function __construct(
        private SellerPaymentRepositoryInterface $repository,
    ){}

    public function execute($seller):  array
    {
        $items = $this->repository->findBySellerAndStatusConfirmed($seller);

        $data = array_map(fn($item) => [
            'id' => $item->getId(),
            'title' => $item->getBook()->getTitle(),
            'quantity' => $item->getQuantity(),
            'format' => $item->getBook()->getFormat()?->value,
            'coverUrl' => $item->getBook()->getCoverUrl(),
            'buyerFirstname' => $item->getOrder()->getShippingFirstname(),
            'buyerLastname' => $item->getOrder()->getShippingLastname(),
            'shippingAddressLine1' => $item->getOrder()->getShippingAddressLine1(),
            'shippingAddressLine2' => $item->getOrder()?->getShippingAddressLine2(),
            'shippingPostalCode' => $item->getOrder()->getShippingPostalCode(),
            'shippingCity' => $item->getOrder()->getShippingCity(),
            'shippingCountry' => $item->getOrder()->getShippingCountry(),
            'createdAt' => $item->getCreatedAt()?->format('d-m-Y'),
            'confirmationToken' => $item->getConfirmationToken(),
        ], $items);

        return $data;
    }
}