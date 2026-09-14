<?php

namespace App\Contacts\Domain\Repository;

use App\Contacts\Domain\Entity\Contact;

interface ContactRepositoryInterface
{
    public function save(Contact $contact): void;

    public function findById(int $id): ?Contact;

    public function deleteContact(Contact $contact): void;

    public function findPaginated(int $page, int $nbre): array;
    
    public function countAll(): int;
}