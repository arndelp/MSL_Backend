<?php

namespace App\SellerPayments\Application\UseCase;

use App\SellerPayments\Domain\Repository\ClaimMessageRepositoryInterface;

final class FindMessageByClaim
{
    public function __construct(
        private ClaimMessageRepositoryInterface $repository,
    ){}

    public function execute($claimId): array
    {
        $messages = $this->repository->findMessageByClaim($claimId);

        $data = array_map(fn($message) => [

           'id' => $message->getId(),
           'createdAt' => $message->getCreatedAt(),
           'message' => $message->getMessage(),
            'author' => $message->getAuthor->getId(),
            'isRead' => $message->getIsRead()
        ], $messages);

        return $data;
    }
}