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
        $mode = $data['mode'] ?? 'client'; // 'admin' ou 'client'

        $user = $em->getRepository(User::class)->findOneBy(['email' => $email]);

        if (!$user) {
            return new JsonResponse(['error' => 'Utilisateur non trouvé'], 401);
        }

        if (!$hasher->isPasswordValid($user, $password)) {
            return new JsonResponse(['error' => 'Mot de passe incorrect'], 401);
        }

        // Vérifier le rôle
        if ($mode === 'admin' && !in_array('ROLE_ADMIN', $user->getRoles())) {
            return new JsonResponse(['error' => 'Ce compte n’est pas un administrateur'], 403);
        }

        if ($mode === 'client' && !in_array('ROLE_CLIENT', $user->getRoles())) {
            return new JsonResponse(['error' => 'Ce compte n’est pas un client'], 403);
        }

        return new JsonResponse(['success' => true]);
    }
}