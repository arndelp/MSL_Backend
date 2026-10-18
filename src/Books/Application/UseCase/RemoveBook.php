<?php

namespace App\Books\Application\UseCase;

use App\Books\Domain\Repository\BookRepositoryInterface;
use App\Shared\Infrastructure\Service\ImageFileManager;

class RemoveBook
{
    public function __construct(
        private BookRepositoryInterface $bookRepository,
        private ImageFileManager $imageFileManager,
    ) {}

    public function execute(int $id): void
    {
        $book = $this->bookRepository->findById($id);

        if (!$book) {
            throw new \InvalidArgumentException('Livre introuvable');
        }

        // Mémoriser les fichiers avant la suppression de l'entité.
        $cover = $book->getCover();
        $images = $book->getImages();

        // Supprimer d'abord le livre en base de données.
        // Si Doctrine échoue, on ne touche pas aux fichiers.
        $this->bookRepository->remove($book);

        // Supprimer ensuite les fichiers physiques.
        $this->imageFileManager->deleteBookImages($cover, $images);
    }
}
