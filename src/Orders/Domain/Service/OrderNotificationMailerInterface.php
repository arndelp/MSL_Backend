<?php 

namespace App\Orders\Domain\Service;

use App\Orders\Domain\Entity\OrderItem;

interface OrderNotificationMailerInterface
{
     public function sendOutOfStockMail(OrderItem $orderItem): void;

    // public function sendOrderConfirmationEmailToBuyer(OrderItem $orderItem): void;
}