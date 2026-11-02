<?php

namespace App\SellerPayments\Application\UseCase;

use App\SellerPayments\Domain\Repository\ClaimMessageRepositoryInterface;

final class SetAllClaimMessageAsRead
{
    public function __construct(
        private ClaimMessageRepositoryInterface $repository,
    ){}

    public function execute(int $userId, int $id): void
    {
        $claimMessages = $this->repository->findMessageByClaimAndNotUser($userId, $id);

        foreach ($claimMessages as $claimMessage) {

            $claimMessage->setIsRead(true);

            $this->repository->save($claimMessage);
        }
    }

}