<?php

namespace App\Orders\Application\DTO;



final class OrderItemCancellationDTO
{
   
    public ?string $reason = null;
   
    public ?string $confirmationToken = null;
}