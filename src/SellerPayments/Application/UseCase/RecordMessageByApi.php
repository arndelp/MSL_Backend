<?php
namespace App\SellerPayments\Application\UseCase;

use App\SellerPayments\Application\DTO\ClaimMessageDTO;
use App\SellerPayments\Application\Mapper\ClaimMessageMapper;
use App\SellerPayments\Domain\Repository\ClaimMessageRepositoryInterface;
use App\SellerPayments\Domain\Repository\ClaimRepositoryInterface;
use App\SellerPayments\Domain\Entity\ClaimMessage;
use App\Users\Domain\Entity\User;

final class RecordMessageByApi
{
    public function __construct(
        private ClaimMessageMapper $claimMessageMapper,
        private ClaimMessageRepositoryInterface $claimMessageRepository,
        private ClaimRepositoryInterface $claimRepository,
    ) {}

    public function execute(ClaimMessageDTO $dto, User $user): ClaimMessage
    {
        $claim = $this->claimRepository->findById($dto->claimId);

        if (!$claim) {
            throw new \InvalidArgumentException('Réclamation introuvable.');
        }

        $claimMessage = $this->claimMessageMapper->toEntity($dto);

        $claimMessage->setAuthor($user);
        $claimMessage->setCreatedAt(
            new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris'))
        );
        $claimMessage->setIsRead(false);
        $claimMessage->setClaim($claim);

        $this->claimMessageRepository->save($claimMessage);

        return $claimMessage;
    }
}