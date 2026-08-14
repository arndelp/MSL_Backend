<?php
namespace App\Enum;
enum OrderItemStatus: string
{
    case CREATED = 'CREATED';    
    case CONFIRMED = 'CONFIRMED';
    case SHIPPED = 'SHIPPED';
    case DELIVERED = 'DELIVERED';
    case RETURNED = 'RETURNED';
    case CANCELLED = 'CANCELLED';    
    case REFUNDED = 'REFUNDED';
}