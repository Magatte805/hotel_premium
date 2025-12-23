<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontendController extends AbstractController
{
    #[Route('/', name: 'app_homepage', methods: ['GET'])]
   #[Route('/{reactRouting}', name: 'app_homepage_react', requirements: ['reactRouting' => '.*'], defaults: ['reactRouting' => ''], methods: ['GET'])]

    public function index(): Response
    {
        // Chemin vers index.html de React build
        $reactBuildPath = $this->getParameter('kernel.project_dir') . '/frontend/dist/index.html';

        if (!file_exists($reactBuildPath)) {
            return new Response('React build not found. Run "npm run build" in frontend.', 500);
        }

        return new Response(file_get_contents($reactBuildPath));
    }
}
