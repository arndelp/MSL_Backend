<?php

namespace App\Enum;
enum PayoutStatus: string
{
    case PENDING = 'pending';
    case SCHEDULED = 'scheduled';
    case PAID = 'paid';
    case FAILED = 'failed';
    case RETURNED = 'returned';
    case CANCELLED = 'cancelled';    
    case BLOCKED = 'blocked';
}