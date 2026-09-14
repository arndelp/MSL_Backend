<?php
namespace App\Orders\Application\UseCase;

use App\Orders\Domain\Entity\Order;
use App\Orders\Domain\Repository\OrderRepositoryInterface;

class GetOrder
{
    public function __construct(private OrderRepositoryInterface $orderRepository) {}

    public function execute(int $id): ?Order
    { 
        $order = $this->orderRepository->findById($id);

    return $order;    
    }
}