<?php

namespace App\SellerPayments\UI\Controller;


use Symfony\Component\HttpFoundation\JsonResponse;
use App\Users\Domain\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\SellerPayments\Application\UseCase\FindSPById;
use Symfony\Bundle\SecurityBundle\Security;
use App\SellerPayments\Domain\Repository\SellerPaymentRepositoryInterface;
use App\SellerPayments\Application\UseCase\FindClaimBySellerPayment;
use App\SellerPayments\Application\UseCase\FindMessageByClaim;



final class ClaimController extends AbstractController
{
    public function __construct(        
        
        private Security $security,
        private FindSPById $findSPById,
        private SellerPaymentRepositoryInterface $sellerPaymentRepository,
        private FindClaimBySellerPayment $findClaimBySellerPayment,
        private FindMessageByClaim $findMessageByClaim,
    ){}

    

    public function findClaimBySellerPayment(int $sellerPaymentId): JsonResponse
    {
        //sécutisation
        $user = $this->security->getUser();

        if (!$user instanceof User) {
            return new JsonResponse(['error' => 'Utilisateur non authentifié'], 401);
        }
        $sellerPayment = $this->sellerPaymentRepository->findById($sellerPaymentId);

        if (!$sellerPayment) {
                throw new NotFoundHttpException(
                    'Paiement vendeur introuvable.'
                );
            }
        
        //Vérification que le $user et soit le vendeur ou l'acheteur de la commande
        $isBuyer = $sellerPayment->getOrder()->getUserId()->getId() === $user->getId();
        $isSeller = $sellerPayment->getSeller()->getId() === $user->getId();

        if (!$isBuyer && !$isSeller) {
            throw new AccessDeniedHttpException(
                'Vous ne pouvez pas consulter ces messages.'
            );
        }

        $data = $this->findClaimBySellerPayment->execute($sellerPaymentId);

        return $this->json($data);

    }

    public function findMessageByClaim(int $claimId): JsonResponse
    {
        //sécutisation
        $user = $this->security->getUser();

        if (!$user instanceof User) {
            return new JsonResponse(['error' => 'Utilisateur non authentifié'], 401);
        }

        $claim = $this->claimRepository->findById($claimId);

        if (!$claim) {
            throw new NotFoundHttpException(
                    'réclamation introuvable.'
                );
        }

        $isBuyer = $claim->getSellerPayment()->getOrder()->getUserId()->getId() === $user->getId();
        $isSeller = $claim->getSellerPayment()->getOrder()->getUserId()->getId() === $user->getId();
   
   
       if (!$isBuyer && !$isSeller) {
            throw new AccessDeniedHttpException(
                'Vous ne pouvez pas consulter ces messages.'
            );
        }
        $data = $this->findMessageByClaim->execute($claimId);

        return $this->json($data);
    }

}