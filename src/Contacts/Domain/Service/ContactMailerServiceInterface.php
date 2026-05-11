<?php

namespace App\Contacts\Domain\Service;

interface ContactMailerServiceInterface
{
    public function send(string $from, string $to,  string $subject, string $content): void;
}