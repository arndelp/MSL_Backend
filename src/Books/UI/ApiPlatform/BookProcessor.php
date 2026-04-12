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
                
        // Récupérer les catégories depuis le contexte de la requête, le contexte  est créé dans le DataTransformer, on peut y accéder ici pour récupérer les catégories envoyées dans la requête
        $request = $context['request'] ?? null; // Récupérer les données brutes de la requête pour les catégories
        $payload = json_decode($request?->getContent() ?? '[]', true); // Décoder le JSON pour extraire les catégories
        $dto->categories = $payload['categories'] ?? []; // Assigner les catégories au DTO
       

        // 👉 on appelle TON usecase
        return $this->useCase->execute($dto);
    }
}