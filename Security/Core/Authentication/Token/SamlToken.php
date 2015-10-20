<?php

namespace Saxid\SimplesamlphpBundle\Security\Core\Authentication\Token;

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;

class SamlToken extends AbstractToken
{
    public function __construct($user, array $roles = array())
    {
        parent::__construct($roles);

        $this->setUser($user);

        if ($roles) {
            $this->setAuthenticated(true);
        }
    }

    public function getCredentials()
    {
        return null;
    }
}
