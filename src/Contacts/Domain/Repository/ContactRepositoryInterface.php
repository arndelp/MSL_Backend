<?php

namespace App\Contacts\Domain\Repository;

use App\Contacts\Domain\Entity\Contact;

interface ContactRepositoryInterface
{
    public function save(Contact $contact): void;
}