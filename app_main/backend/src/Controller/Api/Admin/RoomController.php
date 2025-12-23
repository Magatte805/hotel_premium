<?php

namespace App\Controller\Api\Admin;

use App\Entity\Room;
use App\Repository\HotelRepository;
use App\Repository\RoomRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/admin/rooms', name: 'admin_rooms_')]
class RoomController extends AbstractController
{
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(RoomRepository $roomRepository): JsonResponse
    {
        $rooms = $roomRepository->findAll();
        $data = [];

        foreach ($rooms as $room) {
            $data[] = [
                'id' => $room->getId(),
                'number' => $room->getNumber(),
                'pricePerNight' => $room->getPricePerNight(),
                'hotel' => [
                    'id' => $room->getHotel()?->getId(),
                    'name' => $room->getHotel()?->getName(),
                    'city' => $room->getHotel()?->getCity(),
                ],
            ];
        }

        return $this->json($data);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(
        Request $request,
        EntityManagerInterface $em,
        HotelRepository $hotelRepository
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        if (!is_array($data)) {
            return $this->json(['error' => 'Payload JSON invalide'], 400);
        }

        $hotelId = (int) ($data['hotel_id'] ?? 0);
        $number = $data['number'] ?? null;
        $pricePerNight = $data['pricePerNight'] ?? ($data['price_per_night'] ?? null);

        if (!$hotelId || $number === null || $pricePerNight === null) {
            return $this->json(['error' => 'hotel_id, number, pricePerNight requis'], 400);
        }

        $hotel = $hotelRepository->find($hotelId);
        if (!$hotel) {
            return $this->json(['error' => 'Hotel not found'], 404);
        }

        $room = new Room();
        $room->setHotel($hotel);
        $room->setNumber((int) $number);
        $room->setPricePerNight((float) $pricePerNight);

        $em->persist($room);
        $em->flush();

        return $this->json([
            'id' => $room->getId(),
            'number' => $room->getNumber(),
            'pricePerNight' => $room->getPricePerNight(),
            'hotel' => [
                'id' => $hotel->getId(),
                'name' => $hotel->getName(),
            ],
        ], 201);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(int $id, RoomRepository $roomRepository): JsonResponse

    {
        $room = $roomRepository->find($id);

        if (!$room) {
            return $this->json(['error' => 'Room not found'], 404);
        }

        $data = [
        'id' => $room->getId(),
        'number' => $room->getNumber(),
        'pricePerNight' => $room->getPricePerNight(),
        'hotel' => [
            'id' => $room->getHotel()->getId(),
            'name' => $room->getHotel()->getName()
        ]
    ];


        return $this->json($data);
    }

    #[Route('/{id}', name: 'update', methods: ['PATCH'])]
    public function update(
        int $id,
        Request $request,
        EntityManagerInterface $em,
        RoomRepository $roomRepository,
        HotelRepository $hotelRepository
    ): JsonResponse {
        $room = $roomRepository->find($id);
        if (!$room) {
            return $this->json(['error' => 'Room not found'], 404);
        }

        $data = json_decode($request->getContent(), true);
        if (!is_array($data)) {
            return $this->json(['error' => 'Payload JSON invalide'], 400);
        }

        if (isset($data['hotel_id'])) {
            $hotel = $hotelRepository->find((int) $data['hotel_id']);
            if (!$hotel) {
                return $this->json(['error' => 'Hotel not found'], 404);
            }
            $room->setHotel($hotel);
        }

        if (isset($data['number'])) {
            $room->setNumber((int) $data['number']);
        }

        if (isset($data['pricePerNight']) || isset($data['price_per_night'])) {
            $val = $data['pricePerNight'] ?? $data['price_per_night'];
            $room->setPricePerNight((float) $val);
        }

        $em->flush();

        return $this->json([
            'id' => $room->getId(),
            'number' => $room->getNumber(),
            'pricePerNight' => $room->getPricePerNight(),
            'hotel' => [
                'id' => $room->getHotel()?->getId(),
                'name' => $room->getHotel()?->getName(),
                'city' => $room->getHotel()?->getCity(),
            ],
        ]);
    }
}