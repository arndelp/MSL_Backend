<?php

namespace App\Enum;

enum ShippingMethod: string
{
    case HOME = 'HOME';

    case RELAY = 'RELAY';

    case EXPRESS = 'EXPRESS';

    case PICKUP = 'PICKUP';
}