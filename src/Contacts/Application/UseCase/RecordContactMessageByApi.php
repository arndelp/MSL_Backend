<?php

namespace App\Contacts\Application\UseCase;

use App\Contacts\Application\Mapper\ContactMessageInputMapper;
use App\Contacts\Domain\Entity\Contact;
use App\Contacts\Domain\Repository\ContactRepositoryInterface;
use App\Contacts\Application\DTO\ContactMessageInputDTO;
use Symfony\Bundle\SecurityBundle\Security;

final class RecordContactMessageByApi
{
    public function __construct(
        private ContactRepositoryInterface $contactRepository,
        private Security $security,
        private ContactMessageInputMapper $contactMessageInputMapper
    ) {}

    public function execute(ContactMessageInputDTO $dto): Contact
    {
        $user = $this->security->getUser();
            
                if (!$user) {
                        throw new \Exception('Utilisateur non authentifié');
                    }  

        $contact = $this->contactMessageInputMapper->toEntity($dto);
        $contact->setAuthor($user);
        $contact->setCreatedAt(new \DateTimeImmutable());
        $contact->setIsRead(false); // Par défaut, le message est non lu
        
       

        $this->contactRepository->save($contact);

        return $contact;
    }
}