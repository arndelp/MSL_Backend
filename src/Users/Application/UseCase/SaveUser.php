<?php

namespace App\Users\Application\UseCase;

use Symfony\Component\Mime\Address;
use App\Users\Domain\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use App\Users\Application\DTO\CreateUserDTO;
use App\Users\Application\Mapper\CreateUserMapper;
use App\Users\Infrastructure\Security\EmailVerifierUser;
use App\Users\Domain\Repository\UserRepositoryInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Users\Domain\Repository\EmailDuplicationCheckerInterface;
use App\AuthorProfiles\Domain\Entity\AuthorProfile;

final class SaveUser
{
     public function __construct(
        private UserRepositoryInterface $userRepository,
        private CreateUserMapper $mapper,
        private UserPasswordHasherInterface $passwordHasher,
        private EmailVerifierUser $emailVerifier,
        private Security $security,
        private EmailDuplicationCheckerInterface $emailDuplicationChecker,
    ) {}

    public function execute(CreateUserDTO $dto): User
    {
       
        
       //vérifiation que l'email n'est pas vide
        if (empty($dto->email)) {
            throw new \InvalidArgumentException('L\'email est requis et ne peut pas être vide.');
        }
        // Vérification si l'email existe déjà dans la base de données
        if ($this->emailDuplicationChecker->isEmailDuplicate($dto->email)) {
            throw new \InvalidArgumentException('Cet email est déjà utilisé.');
        }

        // Mapping DTO en Entité par le mapper
        $user = $this->mapper->toEntity($dto);

        

         // Hash du mot de passe (remplace le setPassword dans le mapper)
        $hashedPassword = $this->passwordHasher->hashPassword($user, $dto->password);
        $user->setPassword($hashedPassword);
        
        //$user->setRoles($dto->roles ?? ['ROLE_USER']);
       
        // Sauvegarde en base de données
        $this->userRepository->save($user);
    

// Créer le AuthorProfile lors de l'ajout d'un livre
        // Créer l’AuthorProfile maintenant que l’User a un ID
        if ($user->getType() == 'author') {

            $profile = new AuthorProfile();
            $profile->setUser($user);
            $user->setAuthorProfile($profile);
            $user->setRoles(['ROLE_AUTHOR']); // Assigner le rôle d'auteur à l'utilisateur
            $user->setType('author');
        
        }
        else {
            $user->setRoles(['ROLE_USER']); // Assigner le rôle d'utilisateur standard
            $user->setType('buyer_only');
        }

        $user->setIsVerified(false); // Marquer l'utilisateur comme non vérifié
        $timeZone = new \DateTimeZone('Europe/Paris');
        $user->setCreatedAt(new \DateTimeImmutable(Null, $timeZone));
        $user->setUpdatedAt(new \DateTimeImmutable(Null, $timeZone));
        
        
        // Flush à nouveau pour persister l’AuthorProfile
        $this->userRepository->save($user);

        // Envoyer l'e-mail de confirmation avec mailerVerifier
        $email = (new TemplatedEmail())
           ->from(new Address('admin@monsalondulivre.fr', 'Monsalondulivre.fr'))
           ->to($user->getEmail())
           ->subject('Veuillez confirmer votre e-mail')           
           ->htmlTemplate('emails/email_confirmation_register_user.html.twig');

        $this->emailVerifier->sendEmailConfirmation('app_verify_email_user', $user, $email);


        return $user;
    }
}
