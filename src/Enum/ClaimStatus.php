<?php
namespace App\Enum;

enum ClaimStatus: string
{
    case CREATED = 'CREATED';
    case IN_PROGRESS = 'IN_PROGRESS';
    case RESOLVED = 'RESOLVED';
}