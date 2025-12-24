<?php

namespace App\Tests\Api\Admin;

use App\Entity\Hotel;
use App\Entity\Reservation;
use App\Entity\Room;
use App\Tests\Api\ApiWebTestCase;
use Doctrine\ORM\EntityManagerInterface;

final class AdminReservationApiTest extends ApiWebTestCase
{
    public function testAdminReservationsRequiresAuth(): void
    {
        $client = static::createClient();
        self::migrateOnce();

        $client->request('GET', '/api/admin/reservations');
        $this->assertResponseStatusCodeSame(401);
    }

    public function testAdminReservationsRejectsClientRole(): void
    {
        $client = static::createClient();
        self::migrateOnce();

        $email = 'client.role+admin-resv@example.com';
        $password = 'client1234';
        $this->upsertUser($email, $password, ['ROLE_CLIENT']);

        $client->request('GET', '/api/admin/reservations', server: $this->basicAuth($email, $password));
        // Access control (ROLE_ADMIN) should deny.
        $this->assertContains($client->getResponse()->getStatusCode(), [401, 403]);
    }

    public function testAdminReservationsReturnsCreatedReservation(): void
    {
        $client = static::createClient();
        self::migrateOnce();

        $adminEmail = 'admin.role+admin-resv@example.com';
        $adminPassword = 'admin1234';
        $admin = $this->upsertUser($adminEmail, $adminPassword, ['ROLE_ADMIN']);

        /** @var EntityManagerInterface $em */
        $em = self::getContainer()->get(EntityManagerInterface::class);

        $hotel = new Hotel();
        $hotel->setName('Hotel Test');
        $hotel->setCity('Paris');
        $hotel->setAddress('1 rue de test');
        $em->persist($hotel);

        $room = new Room();
        $room->setHotel($hotel);
        $room->setNumber(101);
        $room->setPricePerNight(120);
        $em->persist($room);

        $reservation = new Reservation();
        $reservation->setUser($admin);
        $reservation->setRoom($room);
        $reservation->setStartDate(new \DateTime('2025-12-20 12:00:00'));
        $reservation->setEndDate(new \DateTime('2025-12-25 12:00:00'));
        $reservation->setStatus('confirmée');
        $em->persist($reservation);

        $em->flush();

        $client->request('GET', '/api/admin/reservations', server: $this->basicAuth($adminEmail, $adminPassword));

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/json');

        $data = $client->getResponse()->toArray();
        $this->assertIsArray($data);

        $found = null;
        foreach ($data as $row) {
            if (($row['id'] ?? null) === $reservation->getId()) {
                $found = $row;
                break;
            }
        }

        $this->assertNotNull($found, 'Created reservation must appear in admin list.');
        $this->assertSame('confirmée', $found['status'] ?? null);
        $this->assertSame('2025-12-20', $found['startDate'] ?? null);
        $this->assertSame('2025-12-25', $found['endDate'] ?? null);

        $this->assertSame($adminEmail, $found['user']['email'] ?? null);
        $this->assertSame($room->getId(), $found['room']['id'] ?? null);
        $this->assertSame($hotel->getId(), $found['hotel']['id'] ?? null);
    }
}


