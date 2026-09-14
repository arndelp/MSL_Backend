<?php

namespace App\Orders\Application\UseCase;

use App\Orders\Domain\Repository\OrderRepositoryInterface;

class DeleteOrder
{
    public function __construct(private OrderRepositoryInterface $orderRepository) {}

    public function execute(int $id): void
    {
    //Appel au repositoryInterface pour utiliser les fonction findById et deleteContact
            
        $order = $this->orderRepository->findById($id);

        if(!$order) {
            return ;   // utile pour le test
        }
        
        $this->orderRepository->remove($order);
    }
}