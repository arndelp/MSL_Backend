<?php
namespace App\SellerPayments\Application\UseCase;

use App\SellerPayments\Domain\Repository\ClaimMessageRepositoryInterface;

final class FindUnreadMessageByUserAndClaim
{
    public function __construct(
        private ClaimMessageRepositoryInterface $repository,
    ){}

    public function execute(int $userId, int $claimId): bool
    {
        return $this->repository->hasMessageNotReadByUserAndClaim($userId, $claimId);
    }

}