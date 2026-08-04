<?php

namespace App\Orders\UI\Controller;

use App\Orders\Application\UseCase\FindOIByStatusandSellerShipped;
use App\Orders\Application\UseCase\FindOIByStatusandSellerConfirmed;
use App\Orders\Application\UseCase\FindOIByStatusandSellerPendingConfirmation;
use App\Users\Domain\Entity\User;
use App\Orders\Domain\Repository\OrderItemRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;



final class OrderItemController extends AbstractController
{

    public function __construct(
            private Security $security,
        
        ) {}

    public function FindOIBySellerPendingConfirmation(FindOIByStatusandSellerPendingConfirmation $findOIByStatusandSellerPendingConfirmation): JsonResponse
    {
        $user = $this->security->getUser();

                if (!$user instanceof User) {
                    return new JsonResponse(['error' => 'Utilisateur non authentifié'], 401);
                }

            $data = $findOIByStatusandSellerPendingConfirmation->execute($user);

            

            return $this->json($data);

    }

    public function FindOIBySellerConfirmed(FindOIByStatusandSellerConfirmed $findOIByStatusandSellerConfirmed): JsonResponse
    {
        
        $user = $this->security->getUser();

                if (!$user instanceof User) {
                    return new JsonResponse(['error' => 'Utilisateur non authentifié'], 401);
                }

            $data = $findOIByStatusandSellerConfirmed->execute($user);

            

            return $this->json($data);
    }

    public function FindOIBySellerShipped(FindOIByStatusandSellerShipped $findOIByStatusandSellerShipped): JsonResponse
    {
        
        $user = $this->security->getUser();

                if (!$user instanceof User) {
                    return new JsonResponse(['error' => 'Utilisateur non authentifié'], 401);
                }

            $data = $findOIByStatusandSellerShipped->execute($user);

            

            return $this->json($data);
    }


    public function CheckConfirmationToken( 
        int $id, 
        Request $request,
        OrderItemRepositoryInterface $orderItemRepository ): JsonResponse 
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