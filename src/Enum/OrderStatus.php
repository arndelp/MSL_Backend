<?php

namespace App\Enum;

enum OrderStatus: string
{
    case PENDING_PAYMENT = 'pending_payment';
    case PENDING = 'pending';
    case PAID = 'paid';
    case PARTIALLY_CANCELLED = 'partially_cancelled';
    CASE PARTIALLY_CONFIRMED = 'partially_confirmed';
    case CONFIRMED = 'confirmed';      
    case CANCELLED = 'cancelled';    
    case COMPLETED = 'completed';
}