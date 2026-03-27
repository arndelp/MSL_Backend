<?php

namespace App\Users\Domain\Repository;

interface EmailDuplicationCheckerInterface
{
    public function isEmailDuplicate(string $email): bool;
}
