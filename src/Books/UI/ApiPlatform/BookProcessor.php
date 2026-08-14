<?php

namespace App\Books\UI\ApiPlatform;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Books\Domain\Entity\Book;
use App\Books\Application\UseCase\RecordBookByApi;
use App\Books\Application\DTO\BookDTO;

class BookProcessor implements ProcessorInterface
{
    public function __construct(
        private RecordBookByApi $useCase
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

    // cover (Vich)
    $dto->cover = $request->files->get('cover');

    // images
    $files = $request->files->get('images', []);

    if ($files instanceof \Symfony\Component\HttpFoundation\File\UploadedFile) {
        $files = [$files];
    }

    $filenames = [];

    foreach ($files as $file) {
        $name = uniqid().'.'.$file->guessExtension();
        $file->move('uploads/images', $name);
        $filenames[] = $name;
    }

    $dto->images = $filenames;

    return $this->useCase->execute($dto);
    }
}