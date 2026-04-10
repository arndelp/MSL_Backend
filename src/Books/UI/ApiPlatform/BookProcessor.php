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
        // 🔥 $data = ton Book déjà rempli par API Platform

        $dto = new BookDTO();
        $dto->title = $data->getTitle();
        $dto->authorName = $data->getAuthorName();
        $dto->price = $data->getPrice();
        $dto->stock = $data->getStock();
        $dto->format = $data->getFormat();
        $dto->description = $data->getDescription();
        $dto->extract = $data->getExtract();
        $dto->isbn = $data->getIsbn();
        $dto->pageCount = $data->getPageCount();
        $dto->currency = $data->getCurrency();
        $dto->categories = []; // optionnel (on peut améliorer après)

        // 👉 on appelle TON usecase
        return $this->useCase->execute($dto);
    }
}