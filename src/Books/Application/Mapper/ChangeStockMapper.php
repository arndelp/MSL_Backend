<?php

namespace App\Books\Application\Mapper;

use App\Books\Application\DTO\ChangeStockDTO;

class ChangeStockMapper
{
   

    public function toEntity(ChangeStockDTO $dto): array
    {
        return [
            'quantity' => $dto->quantity,
        ];
    }

    public function toDTO(array $data): ChangeStockDTO
    {
        $dto = new ChangeStockDTO();
        $dto->quantity = $data['quantity'] ?? null;

        return $dto;
    }
}