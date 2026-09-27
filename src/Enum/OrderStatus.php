<?php

namespace App\Enum;

enum OrderStatus: string
{
    case PENDING_PAYMENT = 'PENDING_PAYMENT';    
    case PAID = 'PAID';
    case PARTIALLY_CONFIRMED = 'PARTIALLY_CONFIRMED'; 
    case CONFIRMED = 'CONFIRMED';      
    case CANCELLED = 'CANCELLED';    
    case COMPLETED = 'COMPLETED';
}