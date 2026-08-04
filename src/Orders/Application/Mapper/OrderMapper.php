<?php

namespace App\Orders\Application\Mapper;

use App\Orders\Domain\Entity\Order;
use App\Orders\Application\DTO\OrderDTO;
use App\Enum\OrderStatus;
use Symfony\Bundle\SecurityBundle\Security;

class OrderMapper
{
    public function __construct(
        private Security $security,
    ){}

    public function toEntity(OrderDTO $orderDTO): Order
{
    $order = new Order();

    

    // Adresse
    $order->setShippingFirstname($orderDTO->shipping_firstname);
    $order->setShippingLastname($orderDTO->shipping_lastname);
    $order->setShippingPhoneNumber($orderDTO->shipping_phone_number);
    $order->setShippingAddressLine1($orderDTO->shipping_address_line_1);
    $order->setShippingAddressLine2($orderDTO->shipping_address_line_2);
    $order->setShippingPostalCode($orderDTO->shipping_postal_code);
    $order->setShippingCity($orderDTO->shipping_city);
    $order->setShippingCountry($orderDTO->shipping_country);

    // Statut
    $order->setStatus(OrderStatus::from($orderDTO->status));

    // Valeurs par défaut
    $order->setCurrency("EUR");
    $order->setTotalAmount(0); // recalculé dans le UseCase
    $order->setCreatedAt(new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris')));
 

    return $order;
}

public function toDTO(Order $order): OrderDTO
{
    $orderDTO = new OrderDTO();
    $orderDTO->shipping_firstname = $order->getShippingFirstname();
    $orderDTO->shipping_lastname = $order->getShippingLastname();
    $orderDTO->shipping_phone_number = $order->getShippingPhoneNumber();
    $orderDTO->shipping_address_line_1 = $order->getShippingAddressLine1();
    $orderDTO->shipping_address_line_2 = $order->getShippingAddressLine2();
    $orderDTO->shipping_postal_code = $order->getShippingPostalCode();
    $orderDTO->shipping_city = $order->getShippingCity();
    $orderDTO->shipping_country = $order->getShippingCountry();
    $orderDTO->status = $order->getStatus()->value;

    return $orderDTO;
}

}