<?php

namespace Hslavich\SimplesamlphpBundle\Security\Http\Logout;

use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Router;

class LogoutSuccessHandler implements LogoutSuccessHandlerInterface
{
    protected $auth;
    protected $router;

    public function __construct($auth, Router $router)
    {
        $this->auth = $auth;
        $this->router = $router;
    }

    public function onLogoutSuccess(Request $request)
    {
        $returnTo = $request->headers->get('referer', '/');
        $request->getSession()->invalidate();

        return new RedirectResponse($this->auth->getLogoutURL($returnTo));
    }
}
