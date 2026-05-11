<?php

namespace App\Contacts\Application\UseCase;

use App\Contacts\Domain\Service\ContactMailerServiceInterface;

class SendMail
{
    public function __construct(
        private ContactMailerServiceInterface $contactMailerService
    ) {}

    public function execute(string $from, string $to, string $subject, string $content): void
    {
        $this->contactMailerService->send($from,$to, $subject,  $content);
    }
}

