<?php

namespace App\Contacts\Application\DTO;

use Symfony\Component\Validator\Constraints as Assert;

final class ContactMessageInputDTO
{
    #[Assert\NotBlank(message: "Le sujet ne peut pas être vide.")] 
    #[Assert\Length(
        max: 255,
        maxMessage: "Le sujet ne peut pas dépasser {{ limit }} caractères."
    )]
    public ?string $subject = null;    

  
    #[Assert\NotBlank(message: "Le message ne peut pas être vide.")]
    public ?string $content = null;

    public function __construct(
       
        string $subject,
        string $content
    ) {
        
        $this->subject = $subject;
        $this->content = $content;
    }
  
}
