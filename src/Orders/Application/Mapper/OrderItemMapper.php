<?php

namespace App\Orders\Application\Mapper;

use App\Orders\Domain\Entity\OrderItem;
use App\Orders\Application\DTO\OrderItemDTO;
use App\Books\Domain\Repository\BookRepositoryInterface;
use App\Enum\OrderItemStatus;
use App\Users\Domain\Repository\UserRepositoryInterface;

class OrderItemMapper
{
    public function __construct(
        private BookRepositoryInterface $bookRepository,
        private UserRepositoryInterface $userRepository
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

        // Vendeur du livre (seller_id)
        $orderItem->setUser($book->getUser());

        // Titre du livre
        $orderItem->setBookTitle($book->getTitle());

        // Statut par défaut
        $orderItem->setStatus(OrderItemStatus::PENDING_PAYMENT);

        // PlateForm Fee
        $orderItem->setPlatformFee(($totalPrice*12)/100);

        
        

       


        //Dates de suivi
        $orderItem->setCreatedAt(new \DateTimeImmutable());
       

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
