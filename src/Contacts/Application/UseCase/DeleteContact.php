<?php

namespace App\Contacts\Application\UseCase;

use App\Contacts\Domain\Repository\ContactRepositoryInterface;

class DeleteContact
{
    public function __construct(private ContactRepositoryInterface $contactRepository) {}

    public function execute(int $id): void
    {
    //Appel au repositoryInterface pour utiliser les fonction findById et deleteContact
            
        $contact = $this->contactRepository->findById($id);

        if(!$contact) {
            return ;   // utile pour le test
        }
        
        $this->contactRepository->deleteContact($contact);
    }
}