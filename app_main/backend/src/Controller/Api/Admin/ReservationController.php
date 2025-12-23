<?php

namespace App\Controller\Api\Admin;

use App\Repository\ReservationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/admin/reservations', name: 'admin_reservations_')]
class ReservationController extends AbstractController
{
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(ReservationRepository $reservationRepository): JsonResponse
    {
        $reservations = $reservationRepository->findBy([], ['id' => 'DESC']);
        $data = [];

        foreach ($reservations as $r) {
            $room = $r->getRoom();
            $hotel = $room?->getHotel();
            $user = $r->getUser();

            $data[] = [
                'id' => $r->getId(),
                'status' => $r->getStatus(),
                'startDate' => $r->getStartDate()?->format('Y-m-d'),
                'endDate' => $r->getEndDate()?->format('Y-m-d'),
                'user' => $user ? [
                    'id' => $user->getId(),
                    'email' => $user->getEmail(),
                    'firstName' => $user->getFirstName(),
                    'lastName' => $user->getLastName(),
                    'phone' => $user->getPhone(),
                ] : null,
                'room' => $room ? [
                    'id' => $room->getId(),
                    'number' => $room->getNumber(),
                    'pricePerNight' => $room->getPricePerNight(),
                ] : null,
                'hotel' => $hotel ? [
                    'id' => $hotel->getId(),
                    'name' => $hotel->getName(),
                    'city' => $hotel->getCity(),
                    'address' => $hotel->getAddress(),
                ] : null,
            ];
        }

        return $this->json($data);
    }
}


