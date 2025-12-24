<?php

namespace App\Tests\Api;

use App\Kernel;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpKernel\KernelInterface;

abstract class ApiWebTestCase extends WebTestCase
{
    private static bool $migrated = false;

    /**
     * Override KernelTestCase::createKernel() to avoid relying on KERNEL_CLASS autoloading,
     * which can be flaky in some Docker + bind-mount environments.
     */
    protected static function createKernel(array $options = []): KernelInterface
    {
        // Force test env so that framework.test services are available for WebTestCase::createClient().
        $env = $options['environment'] ?? 'test';
        $debug = $options['debug'] ?? true;

        return new Kernel($env, (bool) $debug);
    }

    protected static function migrateOnce(): void
    {
        if (self::$migrated) {
            return;
        }
        if (!self::$kernel) {
            throw new \LogicException('Kernel must be booted by createClient() before running migrations.');
        }

        $application = new Application(self::$kernel);
        $application->setAutoExit(false);

        $code = $application->run(
            new ArrayInput([
                'command' => 'doctrine:migrations:migrate',
                '--no-interaction' => true,
            ]),
            new NullOutput()
        );

        self::assertSame(0, $code, 'Migrations must succeed for tests.');
        self::$migrated = true;
    }

    protected function upsertUser(string $email, string $plainPassword, array $roles, ?string $firstName = 'Test', ?string $lastName = 'User', ?string $phone = '000000'): User
    {
        /** @var EntityManagerInterface $em */
        $em = self::getContainer()->get(EntityManagerInterface::class);
        /** @var UserPasswordHasherInterface $hasher */
        $hasher = self::getContainer()->get(UserPasswordHasherInterface::class);

        $repo = $em->getRepository(User::class);
        $user = $repo->findOneBy(['email' => $email]) ?? new User();

        $user
            ->setEmail($email)
            ->setFirstName($firstName ?? '')
            ->setLastName($lastName ?? '')
            ->setPhone($phone ?? '')
            ->setRoles($roles);

        $user->setPassword($hasher->hashPassword($user, $plainPassword));

        $em->persist($user);
        $em->flush();

        return $user;
    }

    /**
     * Symfony BrowserKit HTTP Basic.
     * @return array<string, string>
     */
    protected function basicAuth(string $email, string $password): array
    {
        return [
            'PHP_AUTH_USER' => $email,
            'PHP_AUTH_PW' => $password,
        ];
    }
}


