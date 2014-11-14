<?php

namespace Hslavich\SimplesamlphpBundle\Security\Core\User;

interface SamlUserInterface
{
    public function setSamlAttributes(array $attributes);
}
