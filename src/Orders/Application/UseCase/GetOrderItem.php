<?php
namespace App\Orders\Application\UseCase;

use App\Orders\Domain\Entity\OrderItem;
use App\Orders\Domain\Repository\OrderItemRepositoryInterface;

class GetOrderItem
{
    public function __construct(private OrderItemRepositoryInterface $orderItemRepository) {}

    public function execute(int $id): ?OrderItem
    { 
        $orderItem = $this->orderItemRepository->findById($id);

    return $orderItem;    
    }
}