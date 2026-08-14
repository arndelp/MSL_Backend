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
        
        ) {}

    public function FindSellerPaymentBySellerWaitingConfirmation(FindSPBySellerAndStatusWaitingConfirmation $findSPByStatusandSellerWaitingConfirmation): JsonResponse
    {
        $seller = $this->security->getUser();

                if (!$seller instanceof User) {
                    return new JsonResponse(['error' => 'Utilisateur non authentifié'], 401);
                }

            $data = $findSPByStatusandSellerWaitingConfirmation->execute($seller);

            

            return $this->json($data);

    }

    public function FindSellerPaymentBySellerConfirmed(FindSPBySellerAndStatusConfirmed $findSPByStatusandSellerConfirmed): JsonResponse
    {
        
        $seller = $this->security->getUser();

                if (!$seller instanceof User) {
                    return new JsonResponse(['error' => 'Utilisateur non authentifié'], 401);
                }

            $data = $findSPByStatusandSellerConfirmed->execute($seller);

            

            return $this->json($data);
    }

    public function FindSellerPaymentBySellerShipped(FindSPBySellerAndStatusShipped $findOIByStatusandSellerShipped): JsonResponse
    {
        
        $seller = $this->security->getUser();

                if (!$seller instanceof User) {
                    return new JsonResponse(['error' => 'Utilisateur non authentifié'], 401);
                }

            $data = $findOIByStatusandSellerShipped->execute($seller);

            

            return $this->json($data);
    }


    public function CheckConfirmationToken( 
        int $id, 
        Request $request,
        SellerPaymentRepositoryInterface $orderItemRepository ): JsonResponse 
    {
        $confirmationToken = $request->query->get('confirmationToken');

        if (!$confirmationToken) {
            return new JsonResponse([
                'valid' => false,
                'error' => 'Token manquant'
            ], 400);
        }

        // Vérifie si l’OrderItem existe et si le token est valide
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