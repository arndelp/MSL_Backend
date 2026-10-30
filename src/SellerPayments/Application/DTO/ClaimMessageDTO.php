<?php

namespace App\SellerPayments\Application\DTO;

use Symfony\Component\Validator\Constraints as Assert;

final class ClaimMessageDTO
{
    #[Assert\NotBlank(message: 'Veuilez renseigner votre message.')]
    public ?string $message = null;   

    public int $claimId;
}
