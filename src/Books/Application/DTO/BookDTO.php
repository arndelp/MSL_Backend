<?php

namespace App\Books\Application\DTO;

use Symfony\Component\Validator\Constraints as Assert;

final class BookDTO
{
    #[Assert\NotBlank(message: 'Veuillez remplir ce champ.')]
    #[Assert\Length(max: 100)]
     public ?string $title = null;  

     

    #[Assert\NotBlank(message: 'Veuillez remplir ce champ.')]
    #[Assert\Length(max: 100)]
     public ?string $authorName = null;

     public ?string $author = null;
    
    #[Assert\PositiveOrZero(message: 'Le prix doit être positif ou nul.')]
    #[Assert\Type(type: 'numeric', message: 'Le prix doit être un nombre.')]
    #[Assert\Regex(pattern: '/^\d+(\.\d{1,2})?$/', message: 'Le prix doit être un nombre avec au maximum deux décimales.')]
    #[Assert\NotBlank(message: 'Veuillez remplir ce champ.')]
    #[Assert\Range(min: 0, max: 999999.99, notInRangeMessage: 'Le prix doit être compris entre {{ min }} et {{ max }}.')]
     public ?float $price = null;

    #[Assert\NotBlank(message: 'Veuillez remplir ce champ.')]
     public ?int $stock = null;

    #[Assert\NotBlank(message: 'Veuillez remplir ce champ.')]
     public ?string $format = null;

    #[Assert\NotBlank(message: 'Veuillez remplir ce champ.')]
     public ?string $description = null;

     public ?string $extract = null;

    
     public ?string $isbn = null;

     public ?int $pageCount = null;

     #[Assert\NotBlank(message: 'Veuillez remplir ce champ.')]
     public ?string $currency = null;

    #[Assert\Count(min: 1, minMessage: "Sélectionnez au moins une catégorie.")]
     public ?array $categories = [];

    public function __construct(
               
        string $title = null,      
        string $authorName = null,
        float $price = null,
        int $stock = null,
        string $format = null,
        string $description = null,
        string $extract = null,
        string $isbn = null,
        int $pageCount = null,
        string $currency = null,
        array $categories = []

    ) {

        $this->title = $title;        
        $this->author = $authorName;
        $this->price = $price;
        $this->stock = $stock;
        $this->format = $format;
        $this->description = $description;
        $this->extract = $extract;
        $this->isbn = $isbn;
        $this->pageCount = $pageCount;
        $this->currency = $currency;
        $this->categories = $categories;
        
    }
   
    
}