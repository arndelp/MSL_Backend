<?php

namespace App\SellerPayments\Application\Mapper;

use App\SellerPayments\Domain\Entity\ClaimMessage;
use App\SellerPayments\Application\DTO\ClaimMessageDTO;

class ClaimMessageMapper
{
    public function toEntity(ClaimMessageDTO $dto): ClaimMessage
    {
        $claimMessage = new ClaimMessage();
        $claimMessage->setMessage($dto->message);        

        return $claimMessage;
    }

    public function toDTO(ClaimMessage $claimMessage): ClaimMessageDTO
    {
        return new ClaimMessageDTO(
            $claimMessage->getMessage(),           
        );

    }
}



