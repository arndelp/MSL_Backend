<?php

namespace App\Orders\Application\UseCase;

use App\Orders\Application\DTO\OrderDTO;
use App\Orders\Domain\Repository\OrderRepositoryInterface;
use App\Orders\Application\Mapper\OrderMapper;
use App\Orders\Application\Mapper\OrderItemMapper;
use App\Orders\Domain\Repository\OrderItemRepositoryInterface;

final class RecordOrderByApi
{
   public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private OrderMapper $orderMapper,
        private OrderItemMapper $orderItemMapper,
        private OrderItemRepositoryInterface $orderItemRepository
    ) {}

    public function execute(OrderDTO $orderDTO): void
    {
        $order = $this->orderMapper->toEntity($orderDTO);

        $total = 0;

        foreach ($orderDTO->order_items as $orderItemDTO) {
            $orderItem = $this->orderItemMapper->toEntity($orderItemDTO);

            // Ajout de l'item à la commande
            $order->addOrderItem($orderItem);

            // Calcul du total
            $total += $orderItem->getTotalPrice();
        }

        // Définir le total de la commande
        $order->setTotalAmount($total);

        // Sauvegarde
        $this->orderRepository->save($order);
    }
}

