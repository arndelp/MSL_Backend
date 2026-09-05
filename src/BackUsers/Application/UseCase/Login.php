<?php

namespace App\BackUsers\Application\UseCase;

use App\BackUsers\Application\DTO\LoginResponseDTO;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


class Login
{
    public function __construct(        
        private readonly AuthenticationUtils $authenticationUtils
    ) {}

    public function execute(): LoginResponseDTO
    {
        $lastUsername = $this->authenticationUtils->getLastUsername();
        $lastError = $this->authenticationUtils->getLastAuthenticationError();


      
        return new LoginResponseDTO($lastUsername, $lastError);
    }
}
