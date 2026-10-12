<?php

namespace App\SellerPayments\Application\Mapper;

use App\SellerPayments\Application\DTO\ClaimDTO;
use App\SellerPayments\Domain\Entity\Claim;
use App\Enum\ClaimReason;

class ClaimMapper
{
    public function toEntity (ClaimDTO $dto): Claim
    {
        $claim = new Claim();
        $claim->setSellerPaymentId($dto->sellerPaymentId);
        $claim->setReason(ClaimReason::from($dto->reason));
       
        return $claim;
    }

    public function toDTO (Claim $claim): ClaimDTO
    {
        $dto = new ClaimDTO();
        $dto->sellerPaymentId = $claim->getSellerPaymentId();
        $dto->reason = $claim->getReason()->value;
       
        return $dto;
    }
}