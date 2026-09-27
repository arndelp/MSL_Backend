<?php

namespace App\Books\Application\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class BookDTO
{
    #[Assert\NotBlank(message: 'Veuillez remplir ce champ.')]
    #[Assert\Length(max: 100)]
    #[Assert\Regex(pattern: '/^[a-zA-Z0-9\s\-]+$/', message: 'Le titre ne doit contenir que des lettres, des chiffres, des espaces et des tirets.')]
    public ?string $title = null;  

     

    #[Assert\NotBlank(message: 'Veuillez remplir ce champ.')]
    #[Assert\Length(max: 100)]
    public ?string $authorName = null;    
    
    #[Assert\Type(type: 'numeric', message: 'Le prix doit être un nombre.')]    
    #[Assert\NotBlank(message: 'Veuillez remplir ce champ.')]
    #[Assert\Range(min: 1, max: 99999, notInRangeMessage: 'Le prix doit être compris entre 1,00 EUR et 999,00 EUR.')]
    public ?int $price = null;

    #[Assert\NotBlank(message: 'Veuillez remplir ce champ.')]
    public ?int $quantity = null;

    #[Assert\NotBlank(message: 'Veuillez remplir ce champ.')]
    public ?string $format = null;

    #[Assert\NotBlank(message: 'Veuillez remplir ce champ.')]
    #[Assert\Type(type: 'integer', message: 'Le poids doit être un nombre entier.')]
    #[Assert\Range(
    min: 1,
    max: 30000,
    notInRangeMessage: 'Le poids doit être compris entre {{ min }} et {{ max }} g.'
)]
    public ?int $weight = null;

    #[Assert\NotBlank(message: 'Veuillez remplir ce champ.')]
    public ?string $description = null;

    public ?string $extract = null;

    public ?string $isbn = null;

    #[Assert\NotBlank(message: 'Veuillez remplir ce champ.')]
    public ?int $pageCount = null;

    #[Assert\NotBlank(message: 'Veuillez remplir ce champ.')]
    public ?string $currency = null;

    #[Assert\Count(min: 1, minMessage: "Sélectionnez au moins une catégorie.")]
    public ?array $categories = [];

    public ?UploadedFile $cover = null;

    public ?array $images = [];  
   
    
}