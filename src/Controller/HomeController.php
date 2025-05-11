<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ProjectRepository;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(ProjectRepository $repository): Response
    {
        $allProjects = $repository->findBy(['archived' => 0]);

        return $this->render('project/index.html.twig', [
            'all_projects' => $allProjects,
        ]);
    }
}
