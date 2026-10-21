<?php

namespace App\SellerPayments\Domain\Repository;



interface ClaimMessageRepositoryInterface 
{
   public function findMessageByClaim(int $claimId): array;
}