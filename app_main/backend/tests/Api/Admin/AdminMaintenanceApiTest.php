<?php

namespace App\Tests\Api\Admin;

use App\Entity\Hotel;
use App\Entity\Maintenance;
use App\Entity\Room;
use App\Tests\Api\ApiWebTestCase;
use Doctrine\ORM\EntityManagerInterface;

final class AdminMaintenanceApiTest extends ApiWebTestCase
{
    public function testAdminMaintenanceRequiresAuth(): void
    {
        $client = static::createClient();
        self::migrateOnce();

        $client->request('GET', '/api/admin/maintenance');
        $this->assertResponseStatusCodeSame(401);
    }

    public function testAdminMaintenanceListContainsOngoingMaintenance(): void
    {
        $client = static::createClient();
        self::migrateOnce();

        $adminEmail = 'admin.role+admin-maint@example.com';
        $adminPassword = 'admin1234';
        $this->upsertUser($adminEmail, $adminPassword, ['ROLE_ADMIN']);

        /** @var EntityManagerInterface $em */
        $em = self::getContainer()->get(EntityManagerInterface::class);

        $hotel = new Hotel();
        $hotel->setName('Hotel Maint Test');
        $hotel->setCity('Lyon');
        $hotel->setAddress('2 rue de test');
        $em->persist($hotel);

        $room = new Room();
        $room->setHotel($hotel);
        $room->setNumber(202);
        $room->setPricePerNight(90);
        $em->persist($room);

        $m = new Maintenance();
        $m->setRoom($room);
        $m->setDescription('Peinture');
        $m->setStartDate(new \DateTime('2025-12-20 08:00:00')); // in the past -> en cours
        $m->setEndDatePlanned(new \DateTime('2025-12-28 18:00:00'));
        $m->setStatus('en cours'); // mirrors controller computeStatus()
        $em->persist($m);
        $em->flush();

        $client->request('GET', '/api/admin/maintenance', server: $this->basicAuth($adminEmail, $adminPassword));
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/json');

        $data = json_decode($client->getResponse()->getContent() ?: '[]', true, 512, JSON_THROW_ON_ERROR);
        $this->assertIsArray($data);

        $found = null;
        foreach ($data as $row) {
            if (($row['id'] ?? null) === $m->getId()) {
                $found = $row;
                break;
            }
        }

        $this->assertNotNull($found, 'Created maintenance must appear in admin list.');
        $this->assertSame('en cours', $found['status'] ?? null);
        $this->assertSame('Peinture', $found['description'] ?? null);
        $this->assertSame($room->getId(), $found['room']['id'] ?? null);
        $this->assertSame($hotel->getId(), $found['hotel']['id'] ?? null);
    }

    public function testAdminCanCreateMaintenanceAndListByRoom(): void
    {
        $client = static::createClient();
        self::migrateOnce();

        $adminEmail = 'admin.role+admin-maint-create@example.com';
        $adminPassword = 'admin1234';
        $this->upsertUser($adminEmail, $adminPassword, ['ROLE_ADMIN']);

        /** @var EntityManagerInterface $em */
        $em = self::getContainer()->get(EntityManagerInterface::class);

        $hotel = new Hotel();
        $hotel->setName('Hotel Maint Create Test');
        $hotel->setCity('Tunis');
        $hotel->setAddress('3 rue de test');
        $em->persist($hotel);

        $room = new Room();
        $room->setHotel($hotel);
        $room->setNumber(303);
        $room->setPricePerNight(110);
        $em->persist($room);
        $em->flush();

        // Create an "en cours" maintenance (startDate in the past)
        $client->request(
            'POST',
            '/api/admin/maintenance',
            server: $this->basicAuth($adminEmail, $adminPassword),
            content: json_encode([
                'room_id' => $room->getId(),
                'description' => 'Climatisation',
                'startDate' => '2000-01-01 08:00:00',
                'endDatePlanned' => '2100-01-01 18:00:00',
            ], JSON_THROW_ON_ERROR)
        );

        $this->assertResponseStatusCodeSame(201);
        $created = json_decode($client->getResponse()->getContent() ?: '{}', true, 512, JSON_THROW_ON_ERROR);
        $this->assertSame('Climatisation', $created['description'] ?? null);
        $this->assertSame('en cours', $created['status'] ?? null, 'Status must be computed as "en cours" for past startDate.');

        // List by room should contain it
        $client->request('GET', '/api/admin/maintenance/rooms/'.$room->getId(), server: $this->basicAuth($adminEmail, $adminPassword));
        $this->assertResponseIsSuccessful();
        $rows = json_decode($client->getResponse()->getContent() ?: '[]', true, 512, JSON_THROW_ON_ERROR);
        $this->assertIsArray($rows);
        $this->assertNotEmpty($rows);

        $found = null;
        foreach ($rows as $row) {
            if (($row['id'] ?? null) === ($created['id'] ?? null)) {
                $found = $row;
                break;
            }
        }
        $this->assertNotNull($found, 'Created maintenance must appear in /rooms/{id} list.');
        $this->assertSame('en cours', $found['status'] ?? null);
    }

    public function testAdminCanMarkMaintenanceDone(): void
    {
        $client = static::createClient();
        self::migrateOnce();

        $adminEmail = 'admin.role+admin-maint-done@example.com';
        $adminPassword = 'admin1234';
        $this->upsertUser($adminEmail, $adminPassword, ['ROLE_ADMIN']);

        /** @var EntityManagerInterface $em */
        $em = self::getContainer()->get(EntityManagerInterface::class);

        $hotel = new Hotel();
        $hotel->setName('Hotel Maint Done Test');
        $hotel->setCity('Sousse');
        $hotel->setAddress('4 rue de test');
        $em->persist($hotel);

        $room = new Room();
        $room->setHotel($hotel);
        $room->setNumber(404);
        $room->setPricePerNight(130);
        $em->persist($room);
        $em->flush();

        // Create a maintenance (en cours)
        $client->request(
            'POST',
            '/api/admin/maintenance',
            server: $this->basicAuth($adminEmail, $adminPassword),
            content: json_encode([
                'room_id' => $room->getId(),
                'description' => 'Plomberie',
                'startDate' => '2000-01-01 08:00:00',
                'endDatePlanned' => '2100-01-01 18:00:00',
            ], JSON_THROW_ON_ERROR)
        );
        $this->assertResponseStatusCodeSame(201);
        $created = json_decode($client->getResponse()->getContent() ?: '{}', true, 512, JSON_THROW_ON_ERROR);
        $id = (int) ($created['id'] ?? 0);
        $this->assertGreaterThan(0, $id);

        // Mark done -> should become "terminé"
        $client->request(
            'PATCH',
            '/api/admin/maintenance/'.$id,
            server: $this->basicAuth($adminEmail, $adminPassword),
            content: json_encode([
                'endDateReal' => '2000-01-02 10:00:00',
            ], JSON_THROW_ON_ERROR)
        );

        $this->assertResponseIsSuccessful();
        $updated = json_decode($client->getResponse()->getContent() ?: '{}', true, 512, JSON_THROW_ON_ERROR);
        $this->assertSame('terminé', $updated['status'] ?? null);
        $this->assertSame('2000-01-02 10:00:00', $updated['endDateReal'] ?? null);
    }
}


