<?php

namespace App\Users\Infrastructure\Service;


use App\Users\Domain\Entity\User;
use App\Users\Domain\Service\JwtManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;


class LexikJwtManager implements JwtManagerInterface
{
    public function __construct(private JWTTokenManagerInterface $jwtManager) {}

    public function create(User $user): string
    {
        return $this->jwtManager->create($user);
    }
}
