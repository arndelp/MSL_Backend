<?php

namespace App\Users\UI\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Psr\Log\LoggerInterface;
use Throwable;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use App\Users\Infrastructure\Security\EmailVerifierUser;
use App\Users\Domain\Repository\UserRepositoryInterface;
use App\Users\Application\UseCase\SaveUser;
use App\Users\Application\DTO\CreateUserDTO;





class UserController extends AbstractController
{
   
    public function me(Security $security): JsonResponse
    {
        $user = $this->$security->getUser();

        if (!$user) {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        return $this->json([
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'firstname' => $user->getFirstname(),
            'lastname' => $user->getLastname(),
            'type' => $user->getType(),
            'role' => $user->getRoles(),
        ]);
    }

    public function receiveNewUser(Request $request, LoggerInterface $logger, ValidatorInterface $validator, SaveUser $saveUser ): Response 
    {

    

    $data = json_decode($request->getContent(), true);    
    
   
    $logger->info('Données reçues', ['data' => $data]);
        
    if (empty($data)) {
        return new JsonResponse(['error' => 'Données reçues vides'], 400);
    }
    
    $dto = new CreateUserDTO(
        firstname: $data['firstname'] ?? '',
        lastname: $data['lastname'] ?? '',
        email: $data['email'] ?? '',
        type: $data['type'] ?? '',
        password: $data['password'] ?? '',
       
    );   

    // Validation du DTO
    $errors = $validator->validate($dto);
    if (count($errors) > 0) {            
        return new JsonResponse(['error' => 'Validation échouée'], 400);
    }
    try {  

        // On délègue au UseCase
        $user = $saveUser->execute($dto);         

        return new JsonResponse([
            'success' => 'User créé avec succès',
            'id' => $user->getId(),
            'type' => $user->getType()
        ], 201);

    } catch (\InvalidArgumentException $e) {
        // Erreurs métier (ex: email déjà utilisé)
        return new JsonResponse(['error' => $e->getMessage()], 400);

    } catch (Throwable $e) {
       // Log l'erreur
        $logger->error('Erreur serveur1', ['exception' => $e]);

        // Retour JSON
        return new JsonResponse([
            'success' => false,
            'error' => 'Erreur serveur2',
            'message' => $e->getMessage(),  // optionnel en dev
        ], 500);
    }
}








public function verifyUserEmail(Request $request, EmailVerifierUser $emailVerifier, UserRepositoryInterface $userRepository): Response
    {
        $id = $request->query->get('id');

        if (null === $id) {
        return new JsonResponse([
            'success' => false,
            'message' => 'Aucun identifiant fourni.'
        ], 400);
        }

        $user = $userRepository->findById($id);

        if (!$user) {
            return new RedirectResponse('http://localhost:5173/register');
        }

        try {
            $emailVerifier->handleEmailConfirmation($request, $user);

        return new JsonResponse([
            'success' => true,
            'message' => 'Votre email a été vérifié avec succès !'
        ]);

        } catch (VerifyEmailExceptionInterface $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Le lien de confirmation est invalide ou expiré.'
            ], 400);
        }
    }
}