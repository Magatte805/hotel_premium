<?php

namespace App\Controller\Api\Client;

use App\Repository\HotelRepository;
use App\Repository\RoomRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/client/hotels', name: 'client_hotels_')]
class HotelController extends AbstractController
{
    #[Route('', name: 'list', methods: ['GET'])]
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

    #[Route('/{id}/rooms', name: 'rooms', methods: ['GET'])]
    public function rooms(int $id, RoomRepository $roomRepository): JsonResponse
    {
        $rooms = $roomRepository->findBy(['hotel' => $id]);
        $data = [];

        foreach ($rooms as $room) {
            $data[] = [
                'id' => $room->getId(),
                'number' => $room->getNumber(),
                'pricePerNight' => $room->getPricePerNight(),
            ];
        }

        return $this->json($data);
    }
}


