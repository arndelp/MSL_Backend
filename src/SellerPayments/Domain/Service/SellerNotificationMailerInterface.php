<?php

namespace App\SellerPayments\Domain\Service;

use App\SellerPayments\Domain\Entity\SellerPayment;


interface SellerNotificationMailerInterface 
{
    public function sendOrderConfirmation(SellerPayment $sellerPayment): void;

    public function sendSellerPaymentConfirmationEmail(SellerPayment $sellerPayment): void;

    public function sendSellerPaymentCancellationEmail(SellerPayment $sellerPayment): void;
   
}