<?php

namespace App\Users\Domain\Service;

use App\Users\Domain\Entity\User;



interface JwtManagerInterface
{
    public function create(User $user): string;
}
