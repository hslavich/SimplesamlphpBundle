<?php

namespace Hslavich\SimplesamlphpBundle\Security\Http\Logout;

use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Router;

class LogoutSuccessHandler implements LogoutSuccessHandlerInterface
{
    protected $samlAuth;
    protected $router;

    public function __construct($samlAuth, Router $router)
    {
        $this->samlAuth = $samlAuth;
        $this->router = $router;
    }

    public function onLogoutSuccess(Request $request)
    {
        $returnTo = $request->headers->get('referer');
        $request->getSession()->invalidate();

        return new RedirectResponse($this->samlAuth->getLogoutURL($returnTo));
    }
}
