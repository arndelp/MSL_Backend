<?php

namespace App\Orders\Application\UseCase;

use App\Orders\Domain\Repository\OrderRepositoryInterface;

class GetPaginatedOrder
{
    private OrderRepositoryInterface $orderRepository;

    public function __construct(OrderRepositoryInterface $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }
//Appel au repositoryInterface pour utiliser les fonction findPaginated et countAll
    public function execute(int $page, int $nbre): array
    {
        return $this->orderRepository->findPaginated($page, $nbre);
    }
}

