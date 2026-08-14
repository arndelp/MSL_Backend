<?php

namespace App\Enum;


enum SellerPaymentStatus: string
{
    case CREATED = 'CREATED';    

    case WAITING_SELLER_CONFIRMATION = 'WAITING_SELLER_CONFIRMATION';

    case CONFIRMED = 'CONFIRMED';

    case SHIPPED = 'SHIPPED';

    case CAPTURED = 'CAPTURED';

    case DELIVERED = 'DELIVERED';

    case TRANSFER_PENDING = 'TRANSFER_PENDING';

    case TRANSFERRED = 'TRANSFERRED';

    case CANCELLED = 'CANCELLED';

    case REFUNDED = 'REFUNDED';
}