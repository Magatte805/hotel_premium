<?php

namespace App\Controller\Api\Client;

use App\Entity\Reservation;
use App\Repository\ReservationRepository;
use App\Repository\RoomRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/client/reservations', name: 'client_reservation_')]
class ReservationController extends AbstractController
{
    // POST /api/client/reservations
    #[Route('', name: 'create', methods: ['POST'])]
    public function create(
        Request $request,
        EntityManagerInterface $em,
        RoomRepository $roomRepository,
        ReservationRepository $reservationRepository
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        // Chambre
        $room = $roomRepository->find($data['room_id'] ?? 0);
        if (!$room) {
            return $this->json(['error' => 'Room not found'], 404);
        }

        //  Dates
        if (!isset($data['startDate'], $data['endDate'])) {
            return $this->json(['error' => 'Dates are required'], 400);
        }

        $startDate = new \DateTime($data['startDate']);
        $endDate = new \DateTime($data['endDate']);

        if ($startDate >= $endDate) {
            return $this->json(['error' => 'Invalid date range'], 400);
        }

        // Vérification de disponibilité 
        $conflict = $reservationRepository->createQueryBuilder('r')
            ->where('r.room = :room')
            ->andWhere('r.startDate < :endDate')
            ->andWhere('r.endDate > :startDate')
            ->setParameter('room', $room)
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->getQuery()
            ->getOneOrNullResult();

        if ($conflict) {
            return $this->json(
                ['error' => 'Room not available for selected dates'],
                400
            );
        }

        // Création réservation
        $reservation = new Reservation();
        $reservation->setRoom($room);
        $reservation->setStartDate($startDate);
        $reservation->setEndDate($endDate);
        $reservation->setStatus('confirmée');

        // User temporaire (sans JWT)
        if ($this->getUser()) {
            $reservation->setUser($this->getUser());
        }

        $em->persist($reservation);
        $em->flush();

        return $this->json([
            'id' => $reservation->getId(),
            'room' => $room->getId(),
            'startDate' => $startDate->format('Y-m-d'),
            'endDate' => $endDate->format('Y-m-d'),
            'status' => $reservation->getStatus(),
        ], 201);
    }

    // DELETE /api/client/reservations/{id}
    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(
        int $id,
        ReservationRepository $reservationRepository,
        EntityManagerInterface $em
    ): JsonResponse {
        $reservation = $reservationRepository->find($id);

        if (!$reservation) {
            return $this->json(['error' => 'Reservation not found'], 404);
        }

        $em->remove($reservation);
        $em->flush();

        return $this->json(null, 204);
    }
}