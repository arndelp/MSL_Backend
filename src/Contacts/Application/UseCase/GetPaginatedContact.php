<?php

namespace App\Contacts\Application\UseCase;

use App\Contacts\Domain\Repository\ContactRepositoryInterface;

class GetPaginatedContact
{
    private ContactRepositoryInterface $contactRepository;

    public function __construct(ContactRepositoryInterface $contactRepository)
    {
        $this->contactRepository = $contactRepository;
    }
//Appel au repositoryInterface pour utiliser les fonction findPaginated et countAll
    public function execute(int $page, int $nbre): array
    {
        return $this->contactRepository->findPaginated($page, $nbre);
    }
}

