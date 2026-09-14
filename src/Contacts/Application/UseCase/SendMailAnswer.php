<?php

namespace App\Contacts\Application\UseCase;

use App\Contacts\Domain\Service\ContactMailerServiceInterface;

class SendMailAnswer
{
    public function __construct(
        private ContactMailerServiceInterface $contactMailerService
    ) {}

    public function execute(string $from, string $to, string $subject, string $content): void
    {
        $this->contactMailerService->sendContactAnswer($from,$to, $subject,  $content);
    }
}

