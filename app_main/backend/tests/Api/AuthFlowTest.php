<?php

namespace App\Tests\Api;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

final class AuthFlowTest extends WebTestCase
{
    private static bool $migrated = false;

    private static function migrateOnce(): void
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

    private function deleteUserIfExists(string $email): void
    {
        /** @var EntityManagerInterface $em */
        $em = self::getContainer()->get(EntityManagerInterface::class);
        $repo = $em->getRepository(User::class);
        $existing = $repo->findOneBy(['email' => $email]);
        if ($existing) {
            $em->remove($existing);
            $em->flush();
        }
    }

    public function testRegisterThenLoginReturnsClientRole(): void
    {
        $client = static::createClient();
        self::migrateOnce();

        $email = 'test.user+api@example.com';
        $password = 'test1234';

        $this->deleteUserIfExists($email);

        $client->request(
            'POST',
            '/api/register',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode([
                'firstName' => 'Test',
                'lastName' => 'User',
                'phone' => '123456',
                'email' => $email,
                'password' => $password,
            ], JSON_THROW_ON_ERROR)
        );

        $this->assertResponseIsSuccessful();
        $this->assertJson($client->getResponse()->getContent() ?: '');

        $client->request(
            'POST',
            '/api/login',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode([
                'email' => $email,
                'password' => $password,
            ], JSON_THROW_ON_ERROR)
        );

        $this->assertResponseIsSuccessful();
        $this->assertJson($client->getResponse()->getContent() ?: '');
        $this->assertSame('client', $client->getResponse()->toArray()['role'] ?? null);
    }

    public function testRegisterDuplicateEmailReturns400(): void
    {
        $client = static::createClient();
        self::migrateOnce();

        $email = 'test.duplicate@example.com';
        $password = 'test1234';

        $this->deleteUserIfExists($email);

        $payload = json_encode([
            'firstName' => 'Test',
            'lastName' => 'User',
            'phone' => '123456',
            'email' => $email,
            'password' => $password,
        ], JSON_THROW_ON_ERROR);

        $client->request('POST', '/api/register', server: ['CONTENT_TYPE' => 'application/json'], content: $payload);
        $this->assertResponseIsSuccessful();

        $client->request('POST', '/api/register', server: ['CONTENT_TYPE' => 'application/json'], content: $payload);
        $this->assertResponseStatusCodeSame(400);
        $this->assertJson($client->getResponse()->getContent() ?: '');
    }
}


