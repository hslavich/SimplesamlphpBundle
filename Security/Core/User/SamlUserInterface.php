<?php

namespace Saxid\SimplesamlphpBundle\Security\Core\User;

interface SamlUserInterface
{
    public function setSamlAttributes(array $attributes);
}
