<?php

namespace App\SellerPayments\UI\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use App\Users\Domain\Entity\User;
use App\SellerPayments\Application\UseCase\RecordMessageByApi;
use App\SellerPayments\Application\DTO\ClaimMessageDTO;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Psr\Log\LoggerInterface;
use App\SellerPayments\Application\UseCase\FindClaimById;



final class ClaimMessageController extends AbstractController
{
    public function __construct(
        private Security $security,   
        private ValidatorInterface $validator,
        private RecordMessageByApi $recordMessageByApi,
        private FindClaimById $findClaimById
    ) {}

    public function record(Request $request,LoggerInterface $logger): Response 
    {

    //sécutisation
    $user = $this->security->getUser();

    if (!$user instanceof User) {
        return new JsonResponse(['error' => 'Utilisateur non authentifié'], 401);
    }

    

    // Récupération des données JSON à partir de la requête
    $data = json_decode($request->getContent(), true);  

    if (empty($data)) {
        return new JsonResponse(['error' => 'Données reçues vides'], 400);
    }

    // Création du DTO à partir des données reçues
    $dto = new ClaimMessageDTO();
    $dto->message = $data['message'] ?? null;
    $dto->claimId = (int) ($data['claimId'] ?? 0);

    $claim = $this->findClaimById->execute($dto->claimId);

  

    if (!$claim) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException(
                'Réclamation introuvable.'
            );
        }

    $isBuyer = $claim['buyerId'] === $user->getId(); //$claim['buyerId'] car le useCase envoie un tableau
    $isSeller = $claim['sellerId'] === $user->getId();

    if (!$isBuyer && !$isSeller) {
        throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException(
            'Vous ne pouvez pas envoyer de message dans cette conversation.'
        );
    }



    // Validation
    $errors = $this->validator->validate($dto);
    if (count($errors) > 0) {
        return new JsonResponse(['errors' => (string) $errors], 400);
    }

    try {
        //UseCase
        $claimMessage = $this->recordMessageByApi->execute($dto, $user);

        if (!$claimMessage) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException(
                'Réclamation introuvable.'
            );
        }

        //email
        // $sendMail->execute(
        //     from: 'Monsalondulivre.fr <automated@monsalondulivre.fr>',   
        //     to: 'arndelp80@gmail.com',
        //     subject: $contactMessage->getSubject(),
        //     content: $contactMessage->getContent()
        // );
    } catch (\InvalidArgumentException $e) {
            // Erreurs métier (ex: message de contact déjà enregistré)
            return new JsonResponse(['error' => $e->getMessage()], 400);

        } catch (\Throwable $e)  {
            //Erreurs serveur
            $logger->error('Erreur en recevant le nouveau message de contact : ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            return new JsonResponse(['error' => 'Erreur interne serveur.'], 500);
        }     

    return new JsonResponse([
        'success' => true,
        'message' => 'Message envoyé.',
        'id' => $claimMessage->getId(),
    ], 201);
    }
    

       
}