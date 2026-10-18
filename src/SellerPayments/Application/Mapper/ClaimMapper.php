<?php

namespace App\SellerPayments\Application\Mapper;

use App\SellerPayments\Application\DTO\ClaimDTO;
use App\SellerPayments\Domain\Entity\Claim;
use App\Enum\ClaimReason;

class ClaimMapper
{
    public function toEntity(ClaimDTO $dto): Claim
    {
        $claim = new Claim();

        $claim->setReason(
            ClaimReason::from($dto->reason)
        );

        return $claim;
    }
}
