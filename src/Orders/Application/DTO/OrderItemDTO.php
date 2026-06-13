<?php

namespace App\Orders\Application\DTO;

final class OrderItemDTO
{
    public ?int $book_id = null;
    public ?int $quantity = null;

     public function __construct(
        ?int $book_id = null,
        ?int $quantity = null,        
    ) {
        $this->book_id = $book_id;
        $this->quantity = $quantity;        
    }
}