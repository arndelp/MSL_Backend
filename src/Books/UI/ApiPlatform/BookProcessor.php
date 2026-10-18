<?php

namespace App\Books\UI\ApiPlatform;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Books\Domain\Entity\Book;
use App\Books\Application\UseCase\RecordBookByApi;
use App\Books\Application\DTO\BookDTO;
use App\Shared\Infrastructure\Service\ImageConverter;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class BookProcessor implements ProcessorInterface
{
    public function __construct(
        private RecordBookByApi $useCase,
        private ImageConverter $imageConverter,
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Book
    {
       
         $request = $context['request'] ?? null;

        if (!$request) {
            throw new \Exception('Request not found');
        }

        $dto = new BookDTO();

        // ❌ PLUS $data
        $dto->title = $request->request->get('title');
        $dto->authorName = $request->request->get('authorName');
        $dto->price = (float) $request->request->get('price');
        $dto->quantity = (int) $request->request->get('quantity');
        $dto->format = $request->request->get('format');
        $dto->weight = $request->request->get('weight');
        $dto->description = $request->request->get('description');
        $dto->extract = $request->request->get('extract');
        $dto->isbn = $request->request->get('isbn');
        $dto->pageCount = (int) $request->request->get('pageCount');
        $dto->currency = $request->request->get('currency');

        // categories
        $dto->categories = $request->request->all('categories');

        // Cover
        $coverFile = $request->files->get('cover');

        $dto->cover = null;

        if ($coverFile instanceof UploadedFile) {
            $dto->cover = $this->imageConverter->convertToWebp($coverFile,'covers');
        }


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