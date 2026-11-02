<?php

namespace App\SellerPayments\Application\UseCase;

use App\SellerPayments\Domain\Repository\ClaimRepositoryInterface;

final class FindImagesByClaim
{
    public function __construct(
        private ClaimRepositoryInterface $repository,
    ){}

    public function execute(int $id): array
    {
        $claim = $this->repository->findById($id);

        $data =  [

           
           'images' => $claim->getImageUrls(),
           
        ];

        return $data;
    }
}