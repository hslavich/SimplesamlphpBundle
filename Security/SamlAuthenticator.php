<?php

namespace Hslavich\SimplesamlphpBundle\Security;

use Hslavich\SimplesamlphpBundle\Security\Core\Authentication\Token\SamlToken;
use Hslavich\SimplesamlphpBundle\Security\Core\User\SamlUserInterface;
use Hslavich\SimplesamlphpBundle\Exception\MissingSamlAuthAttributeException;
use Symfony\Component\Security\Http\Authentication\SimplePreAuthenticatorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class SamlAuthenticator implements SimplePreAuthenticatorInterface
{
    protected $samlAuth;
    protected $session;
    protected $authAttribute;

    public function __construct($samlAuth, Session $session, $authAttribute)
    {
        $this->samlAuth = $samlAuth;
        $this->session = $session;
        $this->authAttribute = $authAttribute;
    }

    public function createToken(Request $request, $providerKey)
    {
        if (!$this->samlAuth->isAuthenticated()) {
            $this->session->clear();
        }

        $this->samlAuth->requireAuth();
        $attributes = $this->samlAuth->getAttributes();

        if (!array_key_exists($this->authAttribute, $attributes)) {
            throw new MissingSamlAuthAttributeException(
                sprintf("Attribute '%s' was not found in SAMLResponse", $this->authAttribute)
            );
        }

        $username = $attributes[$this->authAttribute][0];

        $token = new SamlToken($username);
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

        $authenticatedToken = new SamlToken($user, $user->getRoles());
        $authenticatedToken->setAttributes($token->getAttributes());

        return $authenticatedToken;
    }

    public function supportsToken(TokenInterface $token, $providerKey)
    {
        return $token instanceof SamlToken;
    }
}
