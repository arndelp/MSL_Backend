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

  
   

    

    public function __construct(
        string $firstname,
        string $lastname,
        string $email,
        string $password,
       
        
    ) {        
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->email = $email;
        $this->password = $password;
       
        
    }
}
        /*
        Dans un DTO, le constructeur sert à garantir :
            la complétude des données,
            un contrat clair (tous les champs obligatoires sont fournis dès le départ),
            une meilleure robustesse (moins d’objets « incomplets » qui circulent dans ton appli).
       */


