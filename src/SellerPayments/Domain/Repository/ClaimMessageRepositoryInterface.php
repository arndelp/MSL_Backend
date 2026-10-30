<?php

namespace App\SellerPayments\Domain\Repository;

use App\SellerPayments\Domain\Entity\ClaimMessage;


interface ClaimMessageRepositoryInterface 
{
   public function findMessageByClaim(int $id): array;

   public function save(ClaimMessage $claimMessage): void;
}