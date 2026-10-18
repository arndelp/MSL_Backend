<?php

namespace App\SellerPayments\Application\UseCase;

use App\SellerPayments\Application\DTO\ClaimDTO;
use App\SellerPayments\Application\Mapper\ClaimMapper;
use App\SellerPayments\Domain\Repository\ClaimRepositoryInterface;
use App\SellerPayments\Domain\Repository\SellerPaymentRepositoryInterface;
use App\Orders\Domain\Repository\OrderItemRepositoryInterface;
use App\Enum\ClaimStatus;
use Symfony\Bundle\SecurityBundle\Security;
use App\SellerPayments\Domain\Entity\ClaimMessage;

final class RecordClaimByApi
{
    public function __construct(
        private ClaimMapper $claimMapper,
        private ClaimRepositoryInterface $claimRepository,
        private SellerPaymentRepositoryInterface $sellerPaymentRepository,
        private Security $security,
        private OrderItemRepositoryInterface $orderItemRepository,
    ) {
    }

    public function execute(ClaimDTO $dto)
    {
        $user = $this->security->getUser();

        if (!$user) {
            throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException(
                'Utilisateur non authentifié.'
            );
        }

        $sellerPayment = $this->sellerPaymentRepository->findById($dto->sellerPaymentId);

        if (!$sellerPayment) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException(
                'Paiement vendeur introuvable.'
            );
        }

        // Seul l'acheteur concerné peut ouvrir cette réclamation.
        if ($sellerPayment->getOrder()->getUserId()->getId() !== $user->getId()) {
            throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException(
                'Vous ne pouvez pas ouvrir une réclamation sur cette commande.'
            );
        }

        $claim = $this->claimMapper->toEntity($dto);

        $claim->setSellerPayment($sellerPayment);
        $claim->setBuyer($user);
        $claim->setSeller($sellerPayment->getSeller());
        $claim->setStatus(ClaimStatus::CREATED);
        $claim->setCreatedAt(new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris')));
        $claim->setUpdatedAt(new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris')));

        //ClaimMessage

        $claimMessage = new ClaimMessage();
        $claimMessage->setMessage($dto->message);
        $claimMessage->setClaim($claim);
        $claimMessage->setAuthor($user);
        $claimMessage->setCreatedAt(new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris')));
        $claimMessage->setIsRead(false);

        $claim->addClaimMessage($claimMessage);

        // Vérification de l'existence des orderItems du DTO dans le seller paiement BDD
       

        foreach ($dto->orderItemIds as $orderItemId) {
            $orderItem = $this->orderItemRepository->findById($orderItemId);

            if (!$orderItem) {
                throw new NotFoundHttpException(
                    'Livre commandé introuvable.'
                );
            }

            // Vérifier que cet OrderItem appartient au SellerPayment.
            if (!$sellerPayment->getOrderItems()->contains($orderItem)) {
                throw new AccessDeniedException(
                    'Ce livre ne correspond pas au paiement vendeur.'
                );
            }

            $claim->addOrderItem($orderItem);

            $claim->setImages($dto->images ?? []);

            $this->claimRepository->save($claim);
        }

        



        return $claim;
    }
}
