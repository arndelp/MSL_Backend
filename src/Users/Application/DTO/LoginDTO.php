<?php

namespace App\Users\Application\DTO;

use Symfony\Component\Validator\Constraints as Assert;


class LoginDTO
{
    public function __construct(
        #[Assert\NotBlank(message: "L'email est obligatoire.")]
        #[Assert\Email(message: "L'email n'est pas valide.")]
        #[Assert\Length(max: 50)]
        public string $email,

        #[Assert\NotBlank(message: "Le mot de passe est obligatoire.")]
        public string $password
    ) {}
}