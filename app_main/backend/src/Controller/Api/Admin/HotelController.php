<?php

namespace App\Controller\Api\Admin;

use App\Entity\Hotel;
use App\Repository\HotelRepository;
use App\Repository\RoomRepository; 
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/admin/hotels')]
class HotelController extends AbstractController
{
    #[Route('', name: 'admin_hotels_list', methods: ['GET'])]
    public function list(HotelRepository $hotelRepository): JsonResponse
    {
        $hotels = $hotelRepository->findAll();

        $data = [];
        foreach ($hotels as $hotel) {
            $data[] = [
                'id' => $hotel->getId(),
                'name' => $hotel->getName(),
                'city' => $hotel->getCity(),
                'address' => $hotel->getAddress(),
            ];
        }

        return $this->json($data);
    }

    #[Route('', name: 'admin_hotels_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (!is_array($data)) {
            return $this->json(['error' => 'Payload JSON invalide'], 400);
        }

        $name = $data['name'] ?? null;
        $city = $data['city'] ?? null;
        $address = $data['address'] ?? null;

        if (!$name || !$city || !$address) {
            return $this->json(['error' => 'name, city, address requis'], 400);
        }

        $hotel = new Hotel();
        $hotel->setName((string) $name);
        $hotel->setCity((string) $city);
        $hotel->setAddress((string) $address);

        $em->persist($hotel);
        $em->flush();

        return $this->json([
            'id' => $hotel->getId(),
            'name' => $hotel->getName(),
            'city' => $hotel->getCity(),
            'address' => $hotel->getAddress(),
        ], 201);
    }

    #[Route('/{id}', name: 'admin_hotels_update', methods: ['PATCH'])]
    public function update(int $id, Request $request, HotelRepository $hotelRepository, EntityManagerInterface $em): JsonResponse
    {
        $hotel = $hotelRepository->find($id);
        if (!$hotel) {
            return $this->json(['error' => 'Hotel not found'], 404);
        }

        $data = json_decode($request->getContent(), true);
        if (!is_array($data)) {
            return $this->json(['error' => 'Payload JSON invalide'], 400);
        }

        if (isset($data['name'])) {
            $hotel->setName((string) $data['name']);
        }
        if (isset($data['city'])) {
            $hotel->setCity((string) $data['city']);
        }
        if (isset($data['address'])) {
            $hotel->setAddress((string) $data['address']);
        }

        $em->flush();

        return $this->json([
            'id' => $hotel->getId(),
            'name' => $hotel->getName(),
            'city' => $hotel->getCity(),
            'address' => $hotel->getAddress(),
        ]);
    }

    // --- AJOUTER CETTE METHODE ---
    #[Route('/{id}/rooms', name: 'admin_hotels_rooms', methods: ['GET'])]
    public function rooms(int $id, RoomRepository $roomRepository): JsonResponse
    {
        $rooms = $roomRepository->findBy(['hotel' => $id]);

        $data = [];
        foreach ($rooms as $room) {
            $data[] = [
                'id' => $room->getId(),
                'number' => $room->getNumber(),
                'price' => $room->getPricePerNight(),
            ];
        }

        return $this->json($data);
    }
}