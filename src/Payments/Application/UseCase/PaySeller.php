<?php

namespace App\Payments\Application\UseCase;

use App\Enum\PayoutStatus;
use App\Payments\Domain\Payment\StripeConnectGatewayInterface;
use App\SellerPayments\Domain\Entity\SellerPayment;
use Doctrine\ORM\EntityManagerInterface;

final class PaySeller
{
    public function __construct(
        private StripeConnectGatewayInterface $stripeConnectGateway,
        private EntityManagerInterface $entityManager
    ) {
    }

    public function execute(SellerPayment $sellerPayment): void
    {
        if ($sellerPayment->getPayoutStatus() === PayoutStatus::PAID) {
            return;
        }

        $seller = $sellerPayment->getSeller();

        if (!$seller) {
            throw new \RuntimeException(
                'Vendeur introuvable.'
            );
        }

        $stripeConnectAccountId =
            $seller->getStripeConnectAccountId();

        if (!$stripeConnectAccountId) {
            throw new \RuntimeException(
                'Le vendeur ne possède pas de compte Stripe Connect.'
            );
        }

        $order = $sellerPayment->getOrder();

        if (!$order) {
            throw new \RuntimeException(
                'Commande introuvable.'
            );
        }

        $paymentIntentId =
            $order->getStripePaymentIntentId();

        if (!$paymentIntentId) {
            throw new \RuntimeException(
                'PaymentIntent Stripe introuvable.'
            );
        }

        $amount = (int) $sellerPayment->getSellerAmount();

        if ($amount <= 0) {
            throw new \RuntimeException(
                'Le montant à transférer au vendeur est invalide.'
            );
        }

        try {

            $sellerPayment->setPayoutStatus(
                PayoutStatus::SCHEDULED
            );

            $this->entityManager->flush();

            $transferId =
                $this->stripeConnectGateway->createTransfer(
                    $amount,
                    $sellerPayment->getCurrency() ?? 'EUR',
                    $stripeConnectAccountId,
                    $paymentIntentId
                );

            $sellerPayment->setStripeTransferId(
                $transferId
            );

            $sellerPayment->setPayoutStatus(
                PayoutStatus::PAID
            );

            $sellerPayment->setPaidToSellerAt(
                new \DateTimeImmutable(
                    'now',
                    new \DateTimeZone('Europe/Paris')
                )
            );

            $sellerPayment->setUpdatedAt(
                new \DateTimeImmutable(
                    'now',
                    new \DateTimeZone('Europe/Paris')
                )
            );

            $this->entityManager->flush();

        } catch (\Throwable $e) {

            $sellerPayment->setPayoutStatus(
                PayoutStatus::FAILED
            );

            $sellerPayment->setUpdatedAt(
                new \DateTimeImmutable(
                    'now',
                    new \DateTimeZone('Europe/Paris')
                )
            );

            $this->entityManager->flush();

            throw $e;
        }
    }
}