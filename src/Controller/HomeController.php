<?php

namespace App\Controller;

use App\Entity\Role;
use App\Repository\ProjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    #[IsGranted(Role::IS_AUTHENTICATED)]
    public function index(ProjectRepository $projectRepository): Response
    {
        $lastThree = $projectRepository->lastThree();
        $amount = 0;
        foreach ($projectRepository->findAll() as $project) {
            $amount += $project->getPrice();
        }

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'last_three' => $lastThree,
            'amount' => $amount,
            'project_amount' => count($projectRepository->findAll()),
        ]);
    }
}
