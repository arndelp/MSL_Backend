<?php

namespace App\BackUsers\Infrastructure\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class LoginAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    public function __construct(private UrlGeneratorInterface $urlGenerator)
    {
    }

    public function authenticate(Request $request): Passport
    {
        // Récupération des données du formulaire
        $username = $request->request->get('username');  // Utilisation de `request->get()`
        $password = $request->request->get('password');
        $csrfToken = $request->request->get('_csrf_token');

        // Sauvegarder le dernier nom d'utilisateur dans la session
        $request->getSession()->set(SecurityRequestAttributes::LAST_USERNAME, $username);

        // Créer le Passport pour l'authentification
        return new Passport(
            new UserBadge($username), // Badge pour l'utilisateur
            new PasswordCredentials($password), // Badge pour le mot de passe
            [
                new CsrfTokenBadge('authenticate', $csrfToken), // Validation du CSRF token
                new RememberMeBadge(), // Support du "Remember me"
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // Rediriger vers la dernière URL cible si elle existe
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        // Sinon, rediriger vers une route par défaut
        return new RedirectResponse($this->urlGenerator->generate('concert.list.filtered'));  // Exemple de route
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
