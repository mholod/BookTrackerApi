<?php

declare(strict_types=1);

namespace App\Controller\Auth;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class TokenRefreshAction extends AbstractController
{
    #[Route('/api/auth/token-refresh', name: 'api.auth.token-refresh')]
    public function logout(): never
    {
        // Symfony intercepts this automatically based on firewall configuration.
        throw new \LogicException('This method is intercepted by Symfony during logout.');
    }
}