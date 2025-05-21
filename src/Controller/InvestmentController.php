<?php

namespace App\Controller;

use App\Entity\Investment;
use App\Form\InvestmentType;
use App\Repository\InvestmentRepository;
use App\Service\CrudActionService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

#[Route('/investment')]
final class InvestmentController extends AbstractController
{
    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    #[Route(name: 'app_investment_index', methods: ['GET'])]
    public function index(Request $request, InvestmentRepository $investmentRepository, CrudActionService $crudActionService): Response
    {
        return $crudActionService->indexAction($request, $investmentRepository, 'findInvestments', 'investment');
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    #[Route('/new', name: 'app_investment_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CrudActionService $crudActionService): Response
    {
        $investment = new Investment();
        return $crudActionService->formLiveComponentAction($request, $investment, 'investment', [
            'title' => 'Nuevo inversión',
//            'ajax' => $request->isXmlHttpRequest()
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/{id}', name: 'app_investment_show', methods: ['GET'])]
    public function show(Request $request, Investment $investment, CrudActionService $crudActionService): Response
    {
        return $crudActionService->showAction($request, $investment, 'investment', 'investment', 'Detalles de la inversión');
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/{id}/edit', name: 'app_investment_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Investment $investment, CrudActionService $crudActionService): Response
    {
        return $crudActionService->formLiveComponentAction($request, $investment, 'investment', [
            'title' => 'Editar inversión',
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/{id}', name: 'app_investment_delete', methods: ['POST'])]
    public function delete(Request $request, Investment $investment, InvestmentRepository $investmentRepository, CrudActionService $crudActionService): Response
    {
        $successMsg = 'Se ha eliminado la inversión.';
        return $crudActionService->deleteAction($request, $investmentRepository, $investment, $successMsg, 'app_investment_index');
    }
}
