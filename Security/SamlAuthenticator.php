<?php

namespace Hslavich\SimplesamlphpBundle\Security;

use Hslavich\SimplesamlphpBundle\Security\Core\Authentication\Token\SamlToken;
use Hslavich\SimplesamlphpBundle\Security\Core\User\SamlUserInterface;
use Symfony\Component\Security\Core\Authentication\SimplePreAuthenticatorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class SamlAuthenticator implements SimplePreAuthenticatorInterface
{
    protected $samlauth;
    protected $session;

    public function __construct($samlauth, Session $session)
    {
        $this->samlauth = $samlauth;
        $this->session = $session;
    }

    public function createToken(Request $request, $providerKey)
    {
        if (!$this->samlauth->isAuthenticated()) {
            $this->session->clear();
        }

        $this->samlauth->requireAuth();
        $attributes = $this->samlauth->getAttributes();

        $token = new SamlToken($attributes['uid'][0]);
        $token->setAttributes($attributes);

        return $token;
    }

    public function authenticateToken(TokenInterface $token, UserProviderInterface $userProvider, $providerKey)
    {
        $username = $token->getUsername();
        $user = $userProvider->loadUserByUsername($username);

        if ($user instanceof SamlUserInterface) {
            $user->setSamlAttributes($token->getAttributes());
        }

        return new SamlToken($user, $user->getRoles());
    }

    public function supportsToken(TokenInterface $token, $providerKey)
    {
        return $token instanceof SamlToken;
    }
}
