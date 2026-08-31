<?php

namespace App\SellerPayments\UI\Controller;

use App\SellerPayments\Application\UseCase\FindSPBySellerAndStatusShipped;
use App\SellerPayments\Application\UseCase\FindSPBySellerAndStatusConfirmed;
use App\SellerPayments\Application\UseCase\FindSPBySellerAndStatusWaitingConfirmation;
use App\Users\Domain\Entity\User;
use App\SellerPayments\Domain\Repository\SellerPaymentRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

final class SellerPaymentController extends AbstractController
{
    public function __construct(
        private Security $security,
        private FindSPBySellerAndStatusShipped $findSPByStatusandSellerShipped,
        private FindSPBySellerAndStatusConfirmed $findSPByStatusandSellerConfirmed,
        private FindSPBySellerAndStatusWaitingConfirmation $findSPByStatusandSellerWaitingConfirmation
    ) {}

    public function FindSellerPaymentBySellerWaitingConfirmation(): JsonResponse
    {
        $seller = $this->security->getUser();

        if (!$seller instanceof User) {
            return new JsonResponse(['error' => 'Utilisateur non authentifié'], 401);
        }

        $data = $this->findSPByStatusandSellerWaitingConfirmation->execute($seller);

        return $this->json($data);
    }

    public function FindSellerPaymentBySellerConfirmed(): JsonResponse
    {
        $seller = $this->security->getUser();

        if (!$seller instanceof User) {
            return new JsonResponse(['error' => 'Utilisateur non authentifié'], 401);
        }

        $data = $this->findSPByStatusandSellerConfirmed->execute($seller);

        return $this->json($data);
    }

    public function FindSellerPaymentBySellerShipped(): JsonResponse
    {
        $seller = $this->security->getUser();

        if (!$seller instanceof User) {
            return new JsonResponse(['error' => 'Utilisateur non authentifié'], 401);
        }

        $data = $this->findSPByStatusandSellerShipped->execute($seller);

        return $this->json($data);
    }

    public function CheckConfirmationToken(
        int $id,
        Request $request,
        SellerPaymentRepositoryInterface $orderItemRepository
    ): JsonResponse {
        $confirmationToken = $request->query->get('confirmationToken');

        if (!$confirmationToken) {
            return new JsonResponse([
                'valid' => false,
                'error' => 'Token manquant'
            ], 400);
        }

        $orderItem = $orderItemRepository->findConfirmationTokenById($id, $confirmationToken);

        if (!$orderItem) {
            return new JsonResponse([
                'valid' => false,
                'error' => 'Token invalide ou expiré'
            ], 400);
        }

        return new JsonResponse([
            'valid' => true
        ]);
    }
}