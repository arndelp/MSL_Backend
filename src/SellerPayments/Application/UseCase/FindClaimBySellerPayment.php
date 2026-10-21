<?php

namespace App\SellerPayments\Application\UseCase;

use App\SellerPayments\Domain\Repository\ClaimRepositoryInterface;

final class FindClaimBySellerPayment
{
    public function __construct(
        private ClaimRepositoryInterface $repository,
    ){}

    public function execute($sellerPaymentId): array
    {
        $claims = $this->repository->findClaimBySP($sellerPaymentId);

        $data = array_map(fn($claim) => [
            'id' => $claim->getId(),
            'reason' => $claim->getReason()->value,
            'createdAt' => $claim->getCreatedAt()?->format('d-m-Y H:i:s'),
            'updatedAt' => $claim->getUpdatedAt()?->format('d-m-Y H:i:s'),
            'images' => $claim->getImages()
        ], $claims);

       return $data;
    }
}