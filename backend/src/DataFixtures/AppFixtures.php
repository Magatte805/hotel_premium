<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Hotel;
use App\Entity\Room;
use App\Entity\Service;
use App\Entity\Reservation;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // 1 Admin
        $admin = new User();
        $admin->setEmail('admin@example.com');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin123'));
        $manager->persist($admin);

        // 2 Clients
        $client1 = new User();
        $client1->setEmail('client1@example.com');
        $client1->setRoles(['ROLE_USER']);
        $client1->setPassword($this->passwordHasher->hashPassword($client1, 'client123'));
        $manager->persist($client1);

        $client2 = new User();
        $client2->setEmail('client2@example.com');
        $client2->setRoles(['ROLE_USER']);
        $client2->setPassword($this->passwordHasher->hashPassword($client2, 'client123'));
        $manager->persist($client2);

        // 1 Hôtel
        $hotel = new Hotel();
        $hotel->setName('Hôtel Premium Central');
        $hotel->setCity('Paris');
        $hotel->setAddress('1 rue de Paris');
        $manager->persist($hotel);

        // 3 Chambres
        $room1 = new Room();
        $room1->setNumber(101);
        $room1->setPricePerNight(120);
        $room1->setHotel($hotel);
        $manager->persist($room1);

        $room2 = new Room();
        $room2->setNumber(102);
        $room2->setPricePerNight(150);
        $room2->setHotel($hotel);
        $manager->persist($room2);

        $room3 = new Room();
        $room3->setNumber(103);
        $room3->setPricePerNight(180);
        $room3->setHotel($hotel);
        $manager->persist($room3);

        // 2 Services
        $service1 = new Service();
        $service1->setName('Spa');
        $manager->persist($service1);

        $service2 = new Service();
        $service2->setName('Piscine');
        $manager->persist($service2);

        // Lier les services aux chambres
        $room1->addService($service1);
        $room2->addService($service2);

        // 1 Réservation
        $reservation = new Reservation();
        $reservation->setUser($client1);
        $reservation->setRoom($room1);
        $reservation->setStartDate(new \DateTime('2025-12-20'));
        $reservation->setEndDate(new \DateTime('2025-12-25'));
        $reservation->setStatus('confirmed');
        $manager->persist($reservation);

        // Sauvegarde finale dans la base
        $manager->flush();
    }
}