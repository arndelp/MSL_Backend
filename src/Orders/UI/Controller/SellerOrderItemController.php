<?php

namespace App\Orders\UI\Controller;

use App\Orders\Application\UseCase\AcceptOrderItem;
use App\Orders\Application\UseCase\RefuseOrderItem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Orders\Application\UseCase\ToBeShipped;
use Symfony\Bundle\SecurityBundle\Security;
use App\Users\Domain\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Psr\Log\LoggerInterface;
use App\Orders\Application\DTO\OrderItemCancellationDTO;
use Symfony\Component\Serializer\SerializerInterface;

final class SellerOrderItemController
{
    public function __construct(
        private Security $security,
        private AcceptOrderItem $acceptOrder,
        private RefuseOrderItem $refuseOrder,
        private ToBeShipped $toBeShipped,
        private LoggerInterface $logger,
        private SerializerInterface $serializer,
    ) {
    }
// Accepter une commande par le vendeur (capture du paiement)
    public function accept(int $id, Request $request ): Response 
    {     

        $data = json_decode($request->getContent(), true);  

        $confirmationToken = $data['confirmationToken'] ?? null;

        if (!$confirmationToken) {
            return new Response(
                'Token de confirmation manquant',
                400
            );
        }

        $this->acceptOrder->execute(
            $id,
            $confirmationToken
        );

        return new Response(
            'Paiement capturé.'
        );
    }
// Refus de la commande par le vendeur
//Utilisatin d'un DTO pour la raison de l'annulation, pour simplifier j'inclus le confirmationToken dans le dto
    public function refuse(
        int $id,
        Request $request,
        OrderItemCancellationDTO $dto,
         ): Response 
        {   

        $data = json_decode($request->getContent(), true);  

            $dto->confirmationToken = $data['confirmationToken'] ?? null;
            $dto->reason = $data['reason'] ?? null;

            if (!$dto->confirmationToken) {
                return new Response(
                    'Token de confirmation manquant',
                    400
                );
            }

            if (!$dto->reason) {
                return new Response(
                    'Raison de l\'annulation manquante',
                    400
                );
            }
     

        $this->refuseOrder->execute($id, $dto);
        return new Response(
            'Paiement annulé.'
        );
    }

// Marquer un élément de commande comme expédié (par le vendeur)
    public function itemShipped(int $id): Response
    {
        $user = $this->security->getUser();

        if (!$user instanceof User) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Utilisateur non authentifié'
            ], 401);
        }

        $this->toBeShipped->execute($id);

        return new JsonResponse([
            'success' => true,
            'message' => 'Élément de commande expédié.'
        ]);
    }
}