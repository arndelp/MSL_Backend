<?php

namespace App\Users\Domain\Repository;

use App\Users\Domain\Entity\User;

interface UserRepositoryInterface {

    public function save(User $user): void;

    public function findById(int $id): ?User;
    
    public function findByEmail(string $email): ?User;   

     public function findOneByStripeAccount(string $stripeAccountId): ?User;
}