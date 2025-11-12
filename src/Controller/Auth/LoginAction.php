<?php

declare(strict_types=1);

namespace App\Controller\Auth;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginAction extends AbstractController
{
    #[Route('/api/auth/login', name: 'api.auth.login')]
    public function login(): never
    {
        throw new \LogicException('This method is intercepted by Symfony during logout.');
    }
}