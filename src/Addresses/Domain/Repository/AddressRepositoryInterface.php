<?php

namespace App\Addresses\Domain\Repository;

interface AddressRepositoryInterface 
{
   public function findAddressByUserId(int $userId): array;
}