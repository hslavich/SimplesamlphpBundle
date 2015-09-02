<?php

namespace Saxid\SimplesamlphpBundle\Security;

use Saxid\SimplesamlphpBundle\Security\Core\Authentication\Token\SamlToken;
use Saxid\SimplesamlphpBundle\Security\Core\User\SamlUserInterface;
use Symfony\Component\Security\Core\Authentication\SimplePreAuthenticatorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;

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

        // uid LDAP attribute name
        if(isset($attributes['uid'][0])) {
            $uid = $attributes['uid'][0];
        }
        // uid SAML 2 attribute name
        elseif(isset($attributes['urn:oid:0.9.2342.19200300.100.1.1'][0])) {
            $uid = $attributes['urn:oid:0.9.2342.19200300.100.1.1'][0];
        }
        else {
            throw new MissingOptionsException('No uid found');
        }

        $token = new SamlToken($uid);
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
