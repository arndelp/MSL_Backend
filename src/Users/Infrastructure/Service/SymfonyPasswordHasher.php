<?php

namespace App\Users\Infrastructure\Service;



use App\Users\Domain\Entity\User;
use App\Users\Domain\Service\PasswordHasherInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SymfonyPasswordHasher implements PasswordHasherInterface
{
    public function __construct(private UserPasswordHasherInterface $hasher) {}

    public function isPasswordValid(User $user, string $password): bool
    {
        return $this->hasher->isPasswordValid($user, $password);
    }
}
