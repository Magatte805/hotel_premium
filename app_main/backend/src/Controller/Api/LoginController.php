<?php

namespace App\Controller\Api;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class LoginController extends AbstractController
{
    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function login(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $hasher): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';

        $user = $em->getRepository(User::class)->findOneBy(['email' => $email]);

        if (!$user) {
            return new JsonResponse(['error' => 'Utilisateur non trouvé'], 401);
        }

        if (!$hasher->isPasswordValid($user, $password)) {
            return new JsonResponse(['error' => 'Mot de passe incorrect'], 401);
        }

        $roles = $user->getRoles();
        $role = in_array('ROLE_ADMIN', $roles, true) ? 'admin' : 'client';

        return new JsonResponse([
            'success' => true,
            'role' => $role,
        ]);
    }
}