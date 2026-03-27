<?php

namespace App\Users\Application\Mapper;

use App\Users\Domain\Entity\User;
use App\Users\Application\DTO\CreateUserDTO;


class CreateUserMapper
{
public function toEntity(CreateUserDTO $dto): User
{
    $user = new User();
    $user->setFirstname($dto->firstname);
    $user->setLastname($dto->lastname);
    $user->setEmail($dto->email);  
    
  
    

    return $user;
}

public function toDTO(User $user): CreateUserDTO
{
    $dto = new CreateUserDTO();    
    $dto->firstname = $user->getFirstname();
    $dto->lastname = $user->getLastname();
    $dto->email = $user->getEmail();
    $dto->password = $user->getPassword();
   
    

    return $dto;
}

}

