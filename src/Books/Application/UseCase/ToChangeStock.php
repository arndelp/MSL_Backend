<?php
namespace App\Books\Application\UseCase;

use App\Books\Domain\Repository\BookRepositoryInterface;
use App\Books\Application\DTO\ChangeStockDTO;
use App\Books\Application\Mapper\ChangeStockMapper;


class ToChangeStock
{
    public function __construct(
        private BookRepositoryInterface $bookRepository, 
        private ChangeStockMapper $changeStockMapper
        ) {}

    public function execute(int $id, int $quantity): Void
    {
        $book = $this->bookRepository->findById($id);

            if (!$book) {
                throw new \InvalidArgumentException('Livre introuvable');
            }

             // Créer le DTO avec la nouvelle quantité
            $dto = new ChangeStockDTO();
            $dto->quantity = $quantity;

            // Récupérer les données mappées (tableau)
            $mappedData = $this->changeStockMapper->toEntity($dto);

            // Appliquer les changements à l'entité Book existante
            // on vérifie d'abort que la clé 'quantity' existe dans le tableau mappé avant de l'utiliser pour éviter les erreurs
            if (isset($mappedData['quantity'])) {
                $book->setQuantity($mappedData['quantity']);
            }

            // Sauvegarder l'entité Book modifiée
            $this->bookRepository->save($book);

           
    }
}
