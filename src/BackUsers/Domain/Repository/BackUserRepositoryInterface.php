<?php

namespace App\BackUsers\Domain\Repository;

use App\BackUsers\Domain\Entity\BackUser;

interface BackUserRepositoryInterface
{
    public function findById(int $id): ?BackUser;

    public function findOneByEmail(string $email): ?BackUser;

    public function save(BackUser $backUser): void;

    public function remove(BackUser $backUser): void;
}
