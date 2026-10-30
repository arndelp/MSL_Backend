<?php

namespace App\SellerPayments\Application\UseCase;

use App\SellerPayments\Domain\Repository\ClaimRepositoryInterface;

final class FindClaimById
{
    public function __construct(
        private ClaimRepositoryInterface $repository,
    ){}

    public function execute($id): ?array
    {
        $claim = $this->repository->findById($id);

        $data = [
            'id' => $claim->getId(),                    
            'sellerId' => $claim->getSeller()->getId(),
            'buyerId' => $claim->getBuyer()->getId(),           
            
        ];
        return $data;
    }
}
