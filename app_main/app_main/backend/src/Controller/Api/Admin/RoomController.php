<?php

namespace App\Controller\Api\Admin;

use App\Repository\RoomRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/admin/rooms', name: 'admin_rooms_')]
class RoomController extends AbstractController
{
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
}