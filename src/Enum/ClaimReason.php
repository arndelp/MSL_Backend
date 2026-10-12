<?php

namespace App\Enum;

enum ClaimReason: string
{
    case BOOK_NOT_RECEIVED = 'BOOK_NOT_RECEIVED';
    case DAMAGED_BOOK = 'DAMAGED_BOOK';
    case WRONG_BOOK = 'WRONG_BOOK';
    case DELIVERY_DELAY = 'DELIVERY_DELAY';
    case PRICE_PAYMENT_ISSUE = 'PRICE_PAYMENT_ISSUE';
    case RETURN_THE_BOOK = 'RETURN_THE_BOOK';
    case OTHER = 'OTHER';
}

