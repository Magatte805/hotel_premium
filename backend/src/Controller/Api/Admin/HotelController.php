<?php

namespace App\Controller\Api\Admin;

use App\Entity\Hotel;
use App\Repository\HotelRepository;
use App\Repository\RoomRepository; 
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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