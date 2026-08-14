<?php

namespace App\Enum;
enum PayoutStatus: string
{
    case PENDING = 'PENDING';
    case SCHEDULED = 'SCHEDULED';
    case PAID = 'PAID';
    case FAILED = 'FAILED';
    case RETURNED = 'RETURNED';
    case CANCELLED = 'CANCELLED';    
    case BLOCKED = 'BLOCKED';
}