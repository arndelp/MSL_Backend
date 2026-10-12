<?php

namespace App\Users\Application\Mapper;

use App\Users\Domain\Entity\User;
use App\Users\Application\DTO\CreateUserDTO;
use App\Enum\SellerTypeStatus;


class CreateUserMapper
{
public function toEntity(CreateUserDTO $dto): User
{
    $user = new User();
    $user->setFirstname($dto->firstname);
    $user->setLastname($dto->lastname);
    $user->setEmail($dto->email);  
    $user->setType($dto->type);
    $user->setSellerType(
    $dto->sellerType !== null
        ? SellerTypeStatus::from($dto->sellerType)
        : null
);
    $user->setSiret($dto->siret);
    
    
    
  
    

    return $user;
}

public function toDTO(User $user): CreateUserDTO
{
    $dto = new CreateUserDTO();    
    $dto->firstname = $user->getFirstname();
    $dto->lastname = $user->getLastname();
    $dto->email = $user->getEmail();
    $dto->password = $user->getPassword();
    $dto->type = $user->getType();
    $dto->sellerType = $user->getSellerType()?->value;
    $dto->siret = $user->getSiret();
   
    

    return $dto;
}

}

