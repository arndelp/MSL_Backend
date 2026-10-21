<?php

namespace App\SellerPayments\Domain\Repository;

use App\SellerPayments\Domain\Entity\Claim;

interface ClaimRepositoryInterface 
{
    public function save(Claim $claim): void;

    public function findClaimBySP(int $sellerPaymentId): array;

    public function findById(int $claimId): ?Claim;
}