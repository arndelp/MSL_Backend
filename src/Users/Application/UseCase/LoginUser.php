<?php

namespace App\Users\Application\UseCase;


use App\Users\Application\DTO\LoginDTO;
use App\Users\Domain\Service\JwtManagerInterface;
use App\Users\Domain\Service\PasswordHasherInterface;
use App\Users\Domain\Repository\UserRepositoryInterface;



class LoginUser
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private PasswordHasherInterface $passwordHasher,
        private JwtManagerInterface $jwtManager
    ) {}

    public function execute(LoginDTO $dto): array
    {
        $user = $this->userRepository->findByEmail($dto->email);

        if (!$user || !$this->passwordHasher->isPasswordValid($user, $dto->password)) {
            throw new \RuntimeException('Identifiants invalides');
        }

        //if (!$user->isIsVerified()) {
        //    throw new \RuntimeException('Email non vérifié');
        //}

        $token = $this->jwtManager->create($user);
        
        return [
            'token' => $token,
            'user' => [    //user : pour adapter avec le frontEnd
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'firstname' => $user->getFirstname(),
                'lastname' => $user->getLastname(),  
            ]
        ];
    }
}
