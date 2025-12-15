<?php

namespace App\Controller\Api\Admin;

use App\Repository\ServiceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/admin/services', name: 'admin_services_')]
class ServiceController extends AbstractController
{
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(ServiceRepository $serviceRepository): JsonResponse
    {
        $services = $serviceRepository->findAll();
        $data = [];

        foreach ($services as $service) {
            $data[] = [
                'id' => $service->getId(),
                'name' => $service->getName(),
                'description' => $service->getDescription(),
                'price' => $service->getPrice(),
            ];
        }

        return $this->json($data);
    }
}