<?php

namespace App\Books\Application\DTO;

class BookFilterDTO
{
    public ?string $status = null;

    public ?string $authorName = null;

    public ?string $search = null;

    public ?int $category = null;

    public ?bool $isVerified = null;

    public function __construct(array $data = [])
    {
        $this->status = $data['status'] ?? null;

        $this->authorName = $data['authorName'] ?? null;

        $this->search = $data['search'] ?? null;

        $this->category = isset($data['category'])
            ? (int) $data['category']
            : null;

        $this->isVerified = $data['isVerified'] ?? null;
    }
}