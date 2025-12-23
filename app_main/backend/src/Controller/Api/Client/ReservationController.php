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
    // GET /api/client/reservations
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(ReservationRepository $reservationRepository): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        $reservations = $reservationRepository->findBy(['user' => $user], ['id' => 'DESC']);
        $data = [];

        foreach ($reservations as $r) {
            $room = $r->getRoom();
            $hotel = $room?->getHotel();

            $data[] = [
                'id' => $r->getId(),
                'status' => $r->getStatus(),
                'startDate' => $r->getStartDate()?->format('Y-m-d'),
                'endDate' => $r->getEndDate()?->format('Y-m-d'),
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

    // POST /api/client/reservations
    #[Route('', name: 'create', methods: ['POST'])]
    public function create(
        Request $request,
        EntityManagerInterface $em,
        RoomRepository $roomRepository,
        ReservationRepository $reservationRepository
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $user = $this->getUser();
        if (!$user) {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

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

        $reservation->setUser($user);

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

        $user = $this->getUser();
        if (!$user) {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        if ($reservation->getUser()?->getId() !== $user->getId()) {
            return $this->json(['error' => 'Forbidden'], 403);
        }

        $em->remove($reservation);
        $em->flush();

        return $this->json(null, 204);
    }
}