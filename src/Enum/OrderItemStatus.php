<?php
namespace App\Enum;
enum OrderItemStatus: string
{
    case PENDING = 'pending_author_confirmation';
    case CONFIRMED = 'confirmed';
    case SHIPPED = 'shipped';
    case DELIVERED = 'delivered';
    case RETURNED = 'returned';
    case CANCELLED = 'cancelled';    
    case REFUNDED = 'refunded';
}