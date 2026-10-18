<?php

namespace App\SellerPayments\UI\ApiPlatform;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\SellerPayments\Domain\Entity\Claim;
use App\SellerPayments\Application\UseCase\RecordClaimByApi;
use App\SellerPayments\Application\DTO\ClaimDTO;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Shared\Infrastructure\Service\ImageConverter;

class ClaimProcessor implements ProcessorInterface
{
    public function __construct (
        private RecordClaimByApi $useCase,
        private ImageConverter $imageConverter,
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

        $dto->orderItemIds = array_map( //convertir les identifiants recçu en entier
            'intval',
            $request->request->all('orderItemIds')
        );


        // images
        $files = $request->files->get('images', []);

        if ($files instanceof UploadedFile) {
            $files = [$files];
        }

        $filenames = [];

        foreach ($files as $file) {
            if (!$file instanceof UploadedFile) {
                continue;
            }

            $filenames[] = $this->imageConverter->convertToWebp($file,'images');
        }

        $dto->images = $filenames;

        return $this->useCase->execute($dto);
    }
}
        


