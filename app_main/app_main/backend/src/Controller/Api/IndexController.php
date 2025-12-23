<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
#[Route('/')]
public function index(): JsonResponse
{
    return $this->json([
        'status' => 'API Symfony OK'
    ]);
}
}