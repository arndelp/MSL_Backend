<?php

namespace App\Enum;

enum CancellationReason: string
{
    case OUT_OF_STOCK = 'OUT_OF_STOCK';
    case REPRINT = 'REPRINT';
    case SHIPPING_DELAY = 'SHIPPING_DELAY';
    case QUALITY_ISSUE = 'QUALITY_ISSUE';
    case STOCK_ERROR = 'STOCK_ERROR';
    case FORCE_MAJEURE = 'FORCE_MAJEURE';
}