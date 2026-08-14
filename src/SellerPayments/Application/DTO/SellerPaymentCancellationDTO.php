<?php

namespace App\SellerPayments\Application\DTO;



final class SellerPaymentCancellationDTO
{
   
    public ?string $reason = null;
   
    public ?string $confirmationToken = null;
}