<?php

namespace App\BackUsers\Application\DTO;

use Symfony\Component\Security\Core\Exception\AuthenticationException;

class LoginResponseDTO
{
    public function __construct(
        public readonly ?string $email,
        public readonly ?AuthenticationException $error
    ) {}
}
