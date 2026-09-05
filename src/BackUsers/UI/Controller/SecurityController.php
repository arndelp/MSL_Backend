<?php

namespace App\BackUsers\UI\Controller;

use App\BackUsers\Application\UseCase\Login;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SecurityController extends AbstractController
{
  
    public function login(Login $loginUseCase): Response
    {
        // Si déjà connecté, redirection
        if ($this->getUser()) {
            return $this->redirectToRoute('/'); // route d'accueil
        }
          // Exécution du Use Case
        $loginResponse = $loginUseCase->execute();

        return $this->render('@BackUser/login.html.twig', [
            'last_username' => $loginResponse->email,
            'error' => $loginResponse->error,
        ]);
    }
    
    public function logout(): void
    {
        throw new \LogicException('Logout is handled by Symfony firewall.');
    }

    
}

