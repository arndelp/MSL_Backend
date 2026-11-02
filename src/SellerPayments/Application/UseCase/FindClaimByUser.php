<?php

namespace App\SellerPayments\Application\UseCase;

use App\SellerPayments\Domain\Repository\ClaimRepositoryInterface;

final class FindClaimByUser
{
    public function __construct(
        private ClaimRepositoryInterface $repository,
    ){}

    public function execute(int $userId): array
    {
       
        $claims = $this->repository->findClaimByUser($userId);

        $data = array_map(fn($claim) => [
            'id' => $claim->getId(),
            'paymentNumber' => $claim->getSellerPayment()->getPaymentNumber(),
            'reason' => $claim->getReason()->value,
            'createdAt' => $claim->getCreatedAt()?->format('d-m H:i'),
            'updatedAt' => $claim->getUpdatedAt()?->format('d-m-Y H:i:s'),
            'images' => $claim->getImages(),
            'hasUnreadMessages' => $claim->getClaimMessages()->exists(
                fn($key, $message) => !$message->getIsRead()
            ),
        ], $claims);

       return $data;
    }
}