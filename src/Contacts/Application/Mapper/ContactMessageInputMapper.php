<?php

namespace App\Contacts\Application\Mapper;

use App\Contacts\Domain\Entity\Contact;
use App\Contacts\Application\DTO\ContactMessageInputDTO;

class ContactMessageInputMapper

{
    public function toEntity(ContactMessageInputDTO $dto): Contact
    {
        $contactMessage = new Contact();
        $contactMessage->setSubject($dto->subject);
        $contactMessage->setContent($dto->content);
        

        return $contactMessage;
    }

    public function toDTO(Contact $contact): ContactMessageInputDTO
    {
        return new ContactMessageInputDTO(
            $contact->getSubject(),
            $contact->getContent()
        );
    }

}

