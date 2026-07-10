<?php
namespace App\Books\Application\DTO;

use Symfony\Component\Validator\Constraints as Assert;  

final class ChangeStockDTO
{
    #[Assert\NotBlank(message: 'Veuillez remplir ce champ.')]
    #[Assert\PositiveOrZero(message: 'La quantité doit être positive ou nulle.')]
    public ?int $quantity = null;
}