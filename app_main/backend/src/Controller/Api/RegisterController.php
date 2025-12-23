<?php

namespace App\Controller\Api;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegisterController extends AbstractController
{
    #[Route('/api/register', name: 'api_register', methods: ['POST'])]
    public function register(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher,
        ValidatorInterface $validator
    ): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!is_array($data)) {
            return $this->json(['error' => 'Payload JSON invalide'], 400);
        }

        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;
        $firstName = $data['firstName'] ?? null;
        $lastName = $data['lastName'] ?? null;
        $phone = $data['phone'] ?? null;

        if (!$email || !$password || !$firstName || !$lastName || !$phone) {
            return $this->json(['error' => 'Nom, prénom, téléphone, email et mot de passe requis'], 400);
        }

        // Vérifier si l’utilisateur existe déjà
        $existingUser = $em->getRepository(User::class)->findOneBy(['email' => $email]);
        if ($existingUser) {
            return $this->json(['error' => 'Email déjà utilisé'], 400);
        }

        $user = new User();
        $user
            ->setEmail((string) $email)
            ->setFirstName((string) $firstName)
            ->setLastName((string) $lastName)
            ->setPhone((string) $phone);

        // Hash du mot de passe
        $hashedPassword = $passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);

        // Public registration always creates a CLIENT account.
        $user->setRoles(['ROLE_CLIENT']);

        $violations = $validator->validate($user);
        if (count($violations) > 0) {
            // Return the first validation error (simple UX for now)
            return $this->json(['error' => (string) $violations[0]->getMessage()], 400);
        }

        $em->persist($user);
        $em->flush();

        return $this->json(['success' => true, 'message' => 'Utilisateur créé avec succès']);
    }
}