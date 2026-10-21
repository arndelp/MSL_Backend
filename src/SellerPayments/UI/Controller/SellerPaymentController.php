<?php

namespace App\SellerPayments\UI\Controller;

use App\SellerPayments\Application\UseCase\FindSPBySellerAndStatusShipped;
use App\SellerPayments\Application\UseCase\FindSPBySellerAndStatusConfirmed;
use App\SellerPayments\Application\UseCase\FindSPBySellerAndStatusWaitingConfirmation;
use App\SellerPayments\Application\UseCase\FindSPByBuyerAndStatusDelivered;
use App\Users\Domain\Entity\User;
use App\SellerPayments\Domain\Repository\SellerPaymentRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\SellerPayments\Application\UseCase\GetSellerPayment;
use App\SellerPayments\Application\UseCase\FindSPById;


final class SellerPaymentController extends AbstractController
{
    public function __construct(
        private Security $security,
        private FindSPBySellerAndStatusShipped $findSPByStatusandSellerShipped,
        private FindSPBySellerAndStatusConfirmed $findSPByStatusandSellerConfirmed,
        private FindSPBySellerAndStatusWaitingConfirmation $findSPByStatusandSellerWaitingConfirmation,
        private GetSellerPayment $getSellerPayment,
        private FindSPByBuyerAndStatusDelivered $findSPByBuyerAndStatusDelivered,
        private FindSPById $findSPById
    ) {}

    public function findSellerPaymentBySellerWaitingConfirmation(): JsonResponse
    {
        $seller = $this->security->getUser();

        if (!$seller instanceof User) {
            return new JsonResponse(['error' => 'Utilisateur non authentifié'], 401);
        }

        $data = $this->findSPByStatusandSellerWaitingConfirmation->execute($seller);

        return $this->json($data);
    }

    public function findSellerPaymentBySellerConfirmed(): JsonResponse
    {
        $seller = $this->security->getUser();

        if (!$seller instanceof User) {
            return new JsonResponse(['error' => 'Utilisateur non authentifié'], 401);
        }

        $data = $this->findSPByStatusandSellerConfirmed->execute($seller);

        return $this->json($data);
    }

    public function findSellerPaymentBySellerShipped(): JsonResponse
    {
        $seller = $this->security->getUser();

        if (!$seller instanceof User) {
            return new JsonResponse(['error' => 'Utilisateur non authentifié'], 401);
        }

        $data = $this->findSPByStatusandSellerShipped->execute($seller);

        return $this->json($data);
    }

    public function checkConfirmationToken(
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

    public function findSellerPaymentDetails( int $id, SellerPaymentRepositoryInterface $sellerPayment): JsonResponse
    {
        $buyer = $this->security->getUser();

        if (!$buyer instanceof User) {
            return new JsonResponse(['error' => 'Utilisateur non authertifié'], 401);
        }

        $data = $this->findSPById->execute($id);

        return $this->json($data);
    }

    public function findSellerPaymentByBuyerDelivered(): JsonResponse
    {
        $buyer = $this->security->getUser();

       

        if (!$buyer instanceof User) {
            return new JsonResponse(['error' => 'Utilisateur non authentifié'], 401);
        }

        $data = $this->findSPByBuyerAndStatusDelivered->execute($buyer);

       

        return $this->json($data);
    }


    public function details(GetSellerPayment $getSellerPayment, int $id): Response
    {
        $sellerPayment = $getSellerPayment->execute($id);

        if(!$sellerPayment) {
            $this->addFlash(type: 'error', message: 'Aucun paiement vendeur trouvé');
            return $this->redirectToRoute('orders.list.alls');
        }

        return $this->render('@SellerPayments/details.html.twig', ['sellerPayment' => $sellerPayment]);
    }

    
    
}