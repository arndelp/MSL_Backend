<?php

namespace App\SellerPayments\Application\DTO;

use Symfony\Component\Validator\Constraints as Assert;

final class ClaimDTO
{
    #[Assert\NotBlank(message: 'Le paiement vendeur est obligatoire.')]
    #[Assert\Positive(message: 'Identifiant invalide.')]
    public ?int $sellerPaymentId = null;

    #[Assert\NotBlank(message: 'Veuillez sélectionner un motif.')]
    public ?string $reason = null;

    #[Assert\NotBlank(message: 'Veuillez décrire votre réclamation.')]
    public ?string $message = null;

    /** @var int[] */
    #[Assert\Count(min: 1, minMessage: 'Sélectionnez au moins un livre.')]
    #[Assert\All([
        new Assert\Type(type: 'integer'),
        new Assert\Positive(message: 'Identifiant de livre invalide.'),
    ])]
    public array $orderItemIds = [];
}
