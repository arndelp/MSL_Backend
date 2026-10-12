<?php

namespace App\SellerPayments\Domain\Repository;

use App\SellerPayments\Domain\Entity\Claim;

interface ClaimRepositoryInterface 
{
    public function save(Claim $claim): void;
}