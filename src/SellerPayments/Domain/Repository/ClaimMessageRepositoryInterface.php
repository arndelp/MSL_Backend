<?php

namespace App\SellerPayments\Domain\Repository;

use App\SellerPayments\Domain\Entity\ClaimMessage;


interface ClaimMessageRepositoryInterface 
{
   public function findMessageByClaim(int $id): array;

   public function save(ClaimMessage $claimMessage): void;

   public function hasMessageNotReadByUser(int $userId): bool;

   public function hasMessageNotReadByUserAndClaim(int $userId, int $claimId): bool;

   public function findMessageByClaimAndNotUser(int $userId, int $claimId): array;
    
}