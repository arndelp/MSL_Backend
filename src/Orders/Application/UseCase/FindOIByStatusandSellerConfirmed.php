<?php

namespace App\Orders\Application\UseCase;


use App\Orders\Domain\Repository\OrderItemRepositoryInterface;

final class FindOIByStatusandSellerConfirmed
{
    public function __construct(
        private OrderItemRepositoryInterface $repository,
    ){}

    public function execute($user):  array
    {
        $items = $this->repository->findByStatusAndSellerConfirmed($user);

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