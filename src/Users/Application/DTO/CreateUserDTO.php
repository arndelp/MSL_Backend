<?php

namespace App\Users\Application\DTO;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

final class CreateUserDTO
{
    
    #[Assert\NotBlank(message: "Le nom est obligatoire.")]
    #[Assert\Length(max: 30)]
    public ?string $lastname = null;

    #[Assert\NotBlank(message: "Le prénom est obligatoire.")]
    #[Assert\Length(max: 30)]
    public ?string $firstname = null;
    
    #[Assert\NotBlank(message: "L'email est obligatoire.")]
    #[Assert\Email(message: "L'email n'est pas valide.")]
    #[Assert\Length(max: 50)]
    #
    public ?string $email = null;

    #[Assert\NotBlank(message: "Le mot de passe est obligatoire.")]
    #[Assert\Length(max: 50)]
    public ?string $password = null;    

    public ?string $sellerType = null; // "INDIVIDUAL" ou "PROFESSIONAL"

    #[Assert\When(
    expression: 'this.sellerType == "PROFESSIONAL"',
    constraints: [
        new Assert\NotBlank(
            message: 'Le SIRET est obligatoire pour un vendeur professionnel.'
        ),
        new Assert\Regex(
            pattern: '/^\d{14}$/',
            message: 'Le SIRET doit contenir exactement 14 chiffres.'
        )
    ]
    )]
    public ?string $siret = null;

    public ?string $type = null; // "buyer_only" ou "author"

    public function __construct(
        string $firstname,
        string $lastname,
        string $email,
        string $password,
        string $type,
        string $sellerType = null,
        string $siret = null,
       
    ) {        
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->email = $email;
        $this->password = $password;
        $this->type = $type;
        $this->sellerType = $sellerType;
        $this->siret = $siret;
       
    }
}
        /*
        Dans un DTO, le constructeur sert à garantir :
            la complétude des données,
            un contrat clair (tous les champs obligatoires sont fournis dès le départ),
            une meilleure robustesse (moins d’objets « incomplets » qui circulent dans ton appli).
       */


