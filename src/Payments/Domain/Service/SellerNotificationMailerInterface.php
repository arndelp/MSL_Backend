<?php

namespace App\Payments\Domain\Service;

use App\Orders\Domain\Entity\OrderItem;

interface SellerNotificationMailerInterface 
{
    public function sendOrderConfirmation(OrderItem $orderItem): void;
}