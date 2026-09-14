<?php

namespace App\Contacts\Application\UseCase;


use App\Contacts\Domain\Entity\Contact;
use App\Contacts\Domain\Repository\ContactRepositoryInterface;

// uniquement une récupération d'un contact par Id => pas de DTO

class GetContact
{
    public function __construct(private ContactRepositoryInterface $contactRepository ) {}

    public function execute(int $id): ?Contact
    { 

        $contact = $this->contactRepository->findById($id);
        $contact->setIsRead(true);
        
        $this->contactRepository->save($contact);
    
    return $contact;    
    }
}