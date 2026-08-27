<?php

namespace App\Payments\Application\UseCase;

use App\Enum\OrderStatus;
use App\Enum\SellerPaymentStatus;
use App\Payments\Domain\Payment\StripeGatewayInterface;
use App\SellerPayments\Domain\Entity\SellerPayment;
use App\SellerPayments\Domain\Repository\SellerPaymentRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

final class FinalizeOrderPayment
{
    public function __construct(
        private SellerPaymentRepositoryInterface $repository,
        private StripeGatewayInterface $stripeGateway,
        private PaySeller $paySeller,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function execute(SellerPayment $sellerPayment): void
    {
        $order = $sellerPayment->getOrder();

        if (!$order) {
            throw new \RuntimeException(
                'Commande introuvable.'
            );
        }

        /*
         * Si un vendeur attend encore une réponse,
         * la commande ne peut pas encore être finalisée.
         */
        if (
            $this->repository->hasPendingSellerPayments($order)
        ) {
            return;
        }

        /*
         * Protection contre une double finalisation.
         */
        if (
            $order->getStatus() === OrderStatus::PAID ||
            $order->getStatus() === OrderStatus::CANCELLED
        ) {
            return;
        }

        $paymentIntentId = $order->getStripePaymentIntentId();

        if (!$paymentIntentId) {
            throw new \RuntimeException(
                'PaymentIntent Stripe introuvable.'
            );
        }

        /*
         * Calcul du montant à capturer
         * uniquement pour les vendeurs confirmés.
         */
        $amountToCapture = 0;

        foreach ($order->getSellerPayments() as $payment) {

            if (
                $payment->getStatus() ===
                SellerPaymentStatus::CONFIRMED
            ) {
                $amountToCapture +=
                    (int) $payment->getTotalAmount();
            }
        }

        /*
         * Aucun vendeur n'a accepté :
         * on annule complètement l'autorisation Stripe.
         */
        if ($amountToCapture <= 0) {

            $this->stripeGateway->cancelPaymentIntent(
                $paymentIntentId
            );

            $order->setStatus(
                OrderStatus::CANCELLED
            );

            $order->setUpdatedAt(
                new \DateTimeImmutable(
                    'now',
                    new \DateTimeZone('Europe/Paris')
                )
            );

            $this->entityManager->flush(); // Avec un save(), la persistance de la modification de l'order n'est pas guarantit , car il s'occupe normalement uniquement de sellerPayment (voir SellerPaymentRepository)
                                            // flush() permet de modifier plusieurs entités
            return;
        }

        /*
         * Au moins un vendeur a accepté :
         * on capture uniquement son montant.
         */
        $this->stripeGateway->capturePaymentIntent(
            $paymentIntentId,
            $amountToCapture
        );

        $order->setPaidAt(
            new \DateTimeImmutable(
                'now',
                new \DateTimeZone('Europe/Paris')
            )
        );

        $order->setStatus(
            OrderStatus::PAID
        );

        $order->setUpdatedAt(
            new \DateTimeImmutable(
                'now',
                new \DateTimeZone('Europe/Paris')
            )
        );

        /*
         * Maintenant que le paiement est capturé,
         * on transfère l'argent aux vendeurs confirmés.
         */
        foreach ($order->getSellerPayments() as $payment) {

            if (
                $payment->getStatus() ===
                SellerPaymentStatus::CONFIRMED
            ) {
                $this->paySeller->execute($payment);
            }
        }

        $this->entityManager->flush();
    }
}