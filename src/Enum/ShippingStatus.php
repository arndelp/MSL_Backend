<?php

namespace App\Enum;

enum ShippingStatus: string
{
    case WAITING_LABEL = 'WAITING_LABEL';

    case LABEL_CREATED = 'LABEL_CREATED';

    case HANDED_TO_CARRIER = 'HANDED_TO_CARRIER';

    case IN_TRANSIT = 'IN_TRANSIT';

    case DELIVERED = 'DELIVERED';

    case LOST = 'LOST';

    case RETURNED = 'RETURNED';
}