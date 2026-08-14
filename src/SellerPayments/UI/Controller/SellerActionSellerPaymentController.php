<?php

namespace App\SellerPayments\UI\Controller;

use App\SellerPayments\Application\UseCase\AcceptSellerPayment;
use App\SellerPayments\Application\UseCase\RefuseSellerPayment;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\SellerPayments\Application\UseCase\ToBeShipped;
use Symfony\Bundle\SecurityBundle\Security;
use App\Users\Domain\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Psr\Log\LoggerInterface;
use App\SellerPayments\Application\DTO\SellerPaymentCancellationDTO;
use Symfony\Component\Serializer\SerializerInterface;

final class SellerActionSellerPaymentController 
{
    public function __construct(
        private Security $security,
        private AcceptSellerPayment $acceptSellerPayment,
        private RefuseSellerPayment $refuseSellerPayment,
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

        $this->acceptSellerPayment->execute(
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
        SellerPaymentCancellationDTO $dto,
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
     

        $this->refuseSellerPayment->execute($id, $dto);
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