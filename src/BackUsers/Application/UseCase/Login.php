<?php

namespace App\BackUsers\Application\UseCase;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class Login
{
    public function __construct(
        private TokenStorageInterface $tokenStorage
    ) {}

    public function execute(string $email, array $roles): array
    {
        $token = new UsernamePasswordToken(
            $email, // On passe juste l'email comme identifiant
            null,
            'main',
            $roles
        );

        $this->tokenStorage->setToken($token);

        return [
            'email' => $email,
            'error' => null,
        ];
    }
}