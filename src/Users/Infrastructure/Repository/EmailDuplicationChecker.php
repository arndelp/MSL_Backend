<?php

namespace App\Users\Infrastructure\Repository;


use Doctrine\ORM\EntityManagerInterface;
use App\Users\Domain\Entity\User;
use App\Users\Domain\Repository\EmailDuplicationCheckerInterface;

class EmailDuplicationChecker implements EmailDuplicationCheckerInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Vérifie si l'email existe déjà dans la base de données
     *
     * @param string $email
     * @return bool
     */
    public function isEmailDuplicate(string $email): bool
    {
        // Chercher un utilisateur avec cet email dans la base de données
        $userRepository = $this->entityManager->getRepository(User::class);
        $user = $userRepository->findOneBy(['email' => $email]);

        // Si un utilisateur existe avec cet email, c'est un doublon
        return $user !== null;
    }
}
