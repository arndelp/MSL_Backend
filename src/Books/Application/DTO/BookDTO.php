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
    
    #[Assert\PositiveOrZero(message: 'Le prix doit être positif ou nul.')]
    #[Assert\Type(type: 'numeric', message: 'Le prix doit être un nombre.')]
    #[Assert\Regex(pattern: '/^\d+(\.\d{1,2})?$/', message: 'Le prix doit être un nombre avec au maximum deux décimales.')]
    #[Assert\NotBlank(message: 'Veuillez remplir ce champ.')]
    #[Assert\Range(min: 0, max: 999999.99, notInRangeMessage: 'Le prix doit être compris entre {{ min }} et {{ max }}.')]
    public ?float $price = null;

    #[Assert\NotBlank(message: 'Veuillez remplir ce champ.')]
    public ?int $quantity = null;

    #[Assert\NotBlank(message: 'Veuillez remplir ce champ.')]
    public ?string $format = null;

    #[Assert\NotBlank(message: 'Veuillez remplir ce champ.')]
    #[Assert\Length(max: 5)]
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