<?php

namespace App\Controller\Api\Admin;

use App\Entity\Maintenance;
use App\Repository\MaintenanceRepository;
use App\Repository\RoomRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/admin/maintenance', name: 'admin_maintenance_')]
class MaintenanceController extends AbstractController
{
    // POST /api/admin/maintenance
    #[Route('', name: 'create', methods: ['POST'])]
    public function create(
        Request $request,
        EntityManagerInterface $em,
        RoomRepository $roomRepository
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $room = $roomRepository->find($data['room_id'] ?? 0);
        if (!$room) {
            return $this->json(['error' => 'Room not found'], 404);
        }

        $maintenance = new Maintenance();
        $maintenance->setRoom($room);
        $maintenance->setDescription($data['description'] ?? '');

        if (isset($data['startDate'])) {
            $maintenance->setStartDate(new \DateTime($data['startDate']));
        }

        if (isset($data['endDatePlanned'])) {
            $maintenance->setEndDatePlanned(new \DateTime($data['endDatePlanned']));
        }

        // Statut automatique
        $this->computeStatus($maintenance);

        $em->persist($maintenance);
        $em->flush();

        return $this->json([
            'id' => $maintenance->getId(),
            'room' => $room->getId(),
            'description' => $maintenance->getDescription(),
            'status' => $maintenance->getStatus(),
            'startDate' => $maintenance->getStartDate()?->format('Y-m-d H:i:s'),
            'endDatePlanned' => $maintenance->getEndDatePlanned()?->format('Y-m-d H:i:s'),
        ], 201);
    }

    // GET /api/admin/maintenance/rooms/{id}
    #[Route('/rooms/{id}', name: 'list_by_room', methods: ['GET'])]
    public function listByRoom(
        int $id,
        RoomRepository $roomRepository,
        MaintenanceRepository $maintenanceRepository
    ): JsonResponse {
        $room = $roomRepository->find($id);
        if (!$room) {
            return $this->json(['error' => 'Room not found'], 404);
        }

        $maintenances = $maintenanceRepository->findBy(['room' => $room]);
        $data = [];

        foreach ($maintenances as $m) {
            $data[] = [
                'id' => $m->getId(),
                'description' => $m->getDescription(),
                'status' => $m->getStatus(),
                'startDate' => $m->getStartDate()?->format('Y-m-d H:i:s'),
                'endDatePlanned' => $m->getEndDatePlanned()?->format('Y-m-d H:i:s'),
                'endDateReal' => $m->getEndDateReal()?->format('Y-m-d H:i:s'),
            ];
        }

        return $this->json($data);
    }

    // PATCH /api/admin/maintenance/{id}
    #[Route('/{id}', name: 'update', methods: ['PATCH'])]
    public function update(
        int $id,
        Request $request,
        MaintenanceRepository $maintenanceRepository,
        EntityManagerInterface $em
    ): JsonResponse {
        $maintenance = $maintenanceRepository->find($id);
        if (!$maintenance) {
            return $this->json(['error' => 'Maintenance not found'], 404);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['description'])) {
            $maintenance->setDescription($data['description']);
        }

        if (isset($data['startDate'])) {
            $maintenance->setStartDate(new \DateTime($data['startDate']));
        }

        if (isset($data['endDatePlanned'])) {
            $maintenance->setEndDatePlanned(new \DateTime($data['endDatePlanned']));
        }

        if (isset($data['endDateReal'])) {
            $maintenance->setEndDateReal(new \DateTime($data['endDateReal']));
        }

        // Statut recalculé automatiquement
        $this->computeStatus($maintenance);

        $em->flush();

        return $this->json([
            'id' => $maintenance->getId(),
            'room' => $maintenance->getRoom()->getId(),
            'description' => $maintenance->getDescription(),
            'status' => $maintenance->getStatus(),
            'startDate' => $maintenance->getStartDate()?->format('Y-m-d H:i:s'),
            'endDatePlanned' => $maintenance->getEndDatePlanned()?->format('Y-m-d H:i:s'),
            'endDateReal' => $maintenance->getEndDateReal()?->format('Y-m-d H:i:s'),
        ]);
    }

   
    // LOGIQUE AUTOMATIQUE DU STATUT
    private function computeStatus(Maintenance $maintenance): void
    {
        $now = new \DateTime();

        if ($maintenance->getEndDateReal() !== null) {
            $maintenance->setStatus('terminé');
            return;
        }

        if (
            $maintenance->getStartDate() !== null &&
            $maintenance->getStartDate() <= $now
        ) {
            $maintenance->setStatus('en cours');
            return;
        }

        $maintenance->setStatus('prévu');
    }
}