<?php

namespace App\Command;

use App\BackUsers\Domain\Entity\BackUser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-admin',
    description: 'Create a back-office administrator'
)]
class CreateAdminUserCommand extends Command
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int {
        $user = new BackUser();

        $user->setFirstname('Arnaud');
        $user->setLastname('Delpierre');
        $user->setEmail('admin@monsalondulivre.fr');
        $user->setRoles(['ROLE_ADMIN']);

        $user->setPassword(
            $this->passwordHasher->hashPassword(
                $user,
                'MonMotDePasse'
            )
        );

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $output->writeln('<info>Administrateur créé avec succès.</info>');

        return Command::SUCCESS;
    }
}