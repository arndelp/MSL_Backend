<?php

namespace App\Orders\Application\Mapper;

use App\Orders\Domain\Entity\OrderItem;
use App\Orders\Application\DTO\OrderItemDTO;
use App\Books\Domain\Repository\BookRepositoryInterface;
use App\Enum\OrderItemStatus;

class OrderItemMapper
{
    public function __construct(
        private BookRepositoryInterface $bookRepository
    ) {}

    public function toEntity(OrderItemDTO $orderItemDTO): OrderItem
    {
        $orderItem = new OrderItem();

        $book = $this->bookRepository->findById($orderItemDTO->book_id);
        if (!$book) {
            throw new \Exception("Livre introuvable");
        }

        $orderItem->setBook($book);
        $orderItem->setQuantity($orderItemDTO->quantity);

        // Prix unitaire = prix du livre
        $unitPrice = (int) ($book->getPrice() * 100);
        $orderItem->setUnitPrice($unitPrice);

        // Total = quantité × prix unitaire
        $totalPrice = $unitPrice * $orderItemDTO->quantity;
        $orderItem->setTotalPrice($totalPrice);

        // Auteur du livre
        $orderItem->setAuthor($book->getAuthor());

        // Titre du livre
        $orderItem->setBookTitle($book->getTitle());

        // Statut par défaut
        $orderItem->setStatus(OrderItemStatus::PENDING);

        return $orderItem;
    }

    public function toDTO(OrderItem $orderItem): OrderItemDTO
    {
        $orderItemDTO = new OrderItemDTO();
        $orderItemDTO->book_id = $orderItem->getBook()->getId();
        $orderItemDTO->quantity = $orderItem->getQuantity();

        return $orderItemDTO;
    }
}
