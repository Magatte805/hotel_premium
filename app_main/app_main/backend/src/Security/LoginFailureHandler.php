<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;

class LoginFailureHandler implements AuthenticationFailureHandlerInterface
{
    public function onAuthenticationFailure($request, AuthenticationException $exception): JsonResponse
    {
        return new JsonResponse([
            'status' => 'error',
            'message' => 'Email ou mot de passe invalide'
        ], 401);
    }
}