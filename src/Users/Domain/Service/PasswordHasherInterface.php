<?php

namespace App\Users\Domain\Service;

use App\Users\Domain\Entity\User;



interface PasswordHasherInterface
{
    public function isPasswordValid(User $user, string $password): bool;
}
