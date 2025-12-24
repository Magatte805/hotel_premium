<?php

namespace App\Controller\Api\Client;

use App\Repository\HotelRepository;
use App\Repository\ReservationRepository;
use App\Repository\RoomRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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
    public function rooms(Request $request, int $id, RoomRepository $roomRepository, ReservationRepository $reservationRepository): JsonResponse
    {
        $rooms = $roomRepository->findBy(['hotel' => $id]);
        $roomIds = array_map(static fn($r) => $r->getId(), $rooms);

        $now = new \DateTimeImmutable('now');

        // Optional: mark rooms occupied for a selected date range (?startDate=YYYY-MM-DD&endDate=YYYY-MM-DD)
        $startDateRaw = (string) $request->query->get('startDate', '');
        $endDateRaw = (string) $request->query->get('endDate', '');
        $hasRange = $startDateRaw !== '' && $endDateRaw !== '';

        $rangeStart = null;
        $rangeEnd = null;
        if ($hasRange) {
            try {
                $rangeStart = new \DateTimeImmutable($startDateRaw.' 00:00:00');
                $rangeEnd = new \DateTimeImmutable($endDateRaw.' 00:00:00');
                if ($rangeStart >= $rangeEnd) {
                    $hasRange = false;
                }
            } catch (\Throwable) {
                $hasRange = false;
            }
        }

        // Mark rooms occupied (today and/or for selected range)
        $occupiedTodayIds = [];
        $occupiedForRangeIds = [];
        if (!empty($roomIds)) {
            // Today (only confirmed)
            $rowsToday = $reservationRepository->createQueryBuilder('r')
                ->select('IDENTITY(r.room) AS room_id')
                ->where('r.room IN (:roomIds)')
                ->andWhere('LOWER(r.status) LIKE :confirmed')
                ->andWhere('r.startDate <= :now')
                ->andWhere('r.endDate >= :now')
                ->setParameter('roomIds', $roomIds)
                ->setParameter('confirmed', '%confirm%')
                ->setParameter('now', $now)
                ->getQuery()
                ->getArrayResult();

            foreach ($rowsToday as $row) {
                if (isset($row['room_id'])) {
                    $occupiedTodayIds[(int) $row['room_id']] = true;
                }
            }

            // Range (any non-cancelled reservation overlaps the selected range)
            if ($hasRange && $rangeStart && $rangeEnd) {
                $rowsRange = $reservationRepository->createQueryBuilder('r2')
                    ->select('IDENTITY(r2.room) AS room_id')
                    ->where('r2.room IN (:roomIds)')
                    ->andWhere('LOWER(r2.status) NOT LIKE :cancel')
                    ->andWhere('r2.startDate < :rangeEnd')
                    ->andWhere('r2.endDate > :rangeStart')
                    ->setParameter('roomIds', $roomIds)
                    ->setParameter('cancel', '%cancel%')
                    ->setParameter('rangeStart', $rangeStart)
                    ->setParameter('rangeEnd', $rangeEnd)
                    ->getQuery()
                    ->getArrayResult();

                foreach ($rowsRange as $row) {
                    if (isset($row['room_id'])) {
                        $occupiedForRangeIds[(int) $row['room_id']] = true;
                    }
                }
            }
        }

        $data = [];

        foreach ($rooms as $room) {
            $rid = (int) $room->getId();
            $occupiedForDates = $hasRange ? isset($occupiedForRangeIds[$rid]) : null;
            $data[] = [
                'id' => $rid,
                'number' => $room->getNumber(),
                'pricePerNight' => $room->getPricePerNight(),
                'occupiedToday' => isset($occupiedTodayIds[$rid]),
                // null when dates not provided, otherwise boolean
                'occupiedForDates' => $occupiedForDates,
            ];
        }

        return $this->json($data);
    }
}






