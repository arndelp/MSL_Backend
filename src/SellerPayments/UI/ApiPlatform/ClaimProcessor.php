<?php

namespace App\SellerPayments\UI\ApiPlatform;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\SellerPayments\Domain\Entity\Claim;
use App\SellerPayments\Application\UseCase\RecordClaimByApi;
use App\SellerPayments\Application\DTO\ClaimDTO;

class ClaimProcessor implements ProcessorInterface
{
    public function __construct (
        private RecordClaimByApi $useCase
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Claim
    {
        $request = $context['request'] ?? null;

        if (!$request) {
            throw new \Exception('Request not found');
        }

        $dto = new ClaimDTO();

        $dto->sellerPaymentId = $request->request->get('sellerPaymentId');
        $dto->reason = $request->request->get('reason');
        $dto->message = $request->request->get('message');

        $dto->orderItems = $request->request->all('orderItems');

        return $this->useCase->execute($dto);
    }
}
        


