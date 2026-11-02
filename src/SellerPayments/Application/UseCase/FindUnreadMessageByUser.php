<?php
namespace App\SellerPayments\Application\UseCase;

use App\SellerPayments\Domain\Repository\ClaimMessageRepositoryInterface;

final class FindUnreadMessageByUser
{
    public function __construct(
        private ClaimMessageRepositoryInterface $repository,
    ){}

    public function execute(int $userId): bool
    {
        return $this->repository->hasMessageNotReadByUser($userId);
    }

}