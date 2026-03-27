<?php

namespace App\Users\UI\Controller;


use App\Users\Application\DTO\LoginDTO;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Users\Application\UseCase\LoginUser;
use Symfony\Component\Validator\Exception\RuntimeException;
use Symfony\Component\Validator\Validator\ValidatorInterface;




class LoginController
{
    public function __construct(private LoginUser $loginUser, private ValidatorInterface $validator) {}

    public function __invoke(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        //création d'un nouveau DTO
        $loginDTO = new LoginDTO(
            $data['email'] ?? '',
            $data['password'] ?? ''
        );
        
        //Validation du DTO
        $errors = $this->validator->validate($loginDTO);
            if (count($errors) > 0) {
        $messages = [];
        foreach ($errors as $error) {
            $messages[] = $error->getMessage();
        }
        return new JsonResponse(['errors' => $messages], 400);
    }

        //appel du useCase
        try {
            $result = $this->loginUser->execute($loginDTO);   
        //Envoie de la réponse JSON au frontEnd         
            return new JsonResponse($result);
        } catch (RuntimeException $e) {
            return new JsonResponse(['error' => $e->getMessage()], 401);
        }
    }
}
