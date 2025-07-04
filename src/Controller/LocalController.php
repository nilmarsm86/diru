<?php

namespace App\Controller;

use App\DTO\Paginator;
use App\Entity\Building;
use App\Entity\Floor;
use App\Entity\Local;
use App\Form\LocalType;
use App\Repository\FloorRepository;
use App\Repository\LocalRepository;
use App\Service\CrudActionService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

#[Route('/local')]
final class LocalController extends AbstractController
{
    #[Route('/{floor}', name: 'app_local_index', methods: ['GET'])]
    public function index(Request $request, LocalRepository $localRepository, Floor $floor): Response
    {
        $filter = $request->query->get('filter', '');
        $amountPerPage = $request->query->get('amount', 10);
        $pageNumber = $request->query->get('page', 1);

        $data = $localRepository->findFloorLocals($floor, $filter, $amountPerPage, $pageNumber);

        $paginator = new Paginator($data, $amountPerPage, $pageNumber);
        if ($paginator->isFromGreaterThanTotal()) {
            $number = ($pageNumber === 1) ? 1 : ($pageNumber - 1);
            return new RedirectResponse($this->generateUrl($request->attributes->get('_route'), [...$request->query->all(), 'page' => $number]), Response::HTTP_SEE_OTHER);
        }

        $template = ($request->isXmlHttpRequest()) ? '_list.html.twig' : 'index.html.twig';

        return $this->render("local/$template", [
            'filter' => $filter,
            'paginator' => $paginator,
            'floor' => $floor
        ]);
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    #[Route('/new/{floor}', name: 'app_local_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CrudActionService $crudActionService, Floor $floor): Response
    {
        $local = new Local();
        return $crudActionService->formLiveComponentAction($request, $local, 'local', [
            'title' => 'Nuevo Local',
            'floor' => $floor
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/{id}', name: 'app_local_show', methods: ['GET'])]
    public function show(Request $request, Local $local, CrudActionService $crudActionService): Response
    {
        return $crudActionService->showAction($request, $local, 'local', 'local', 'Detalles del local');
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/{id}/edit/{floor}', name: 'app_local_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Local $local, CrudActionService $crudActionService, Floor $floor): Response
    {
        return $crudActionService->formLiveComponentAction($request, $local, 'local', [
            'title' => 'Editar Local',
            'floor' => $floor
        ]);
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    #[Route('/{id}/{floor}', name: 'app_local_delete', methods: ['POST'])]
    public function delete(Request $request, Local $local, LocalRepository $localRepository, CrudActionService $crudActionService, Floor $floor): Response
    {
        $successMsg = 'Se ha eliminado el local.';
        return $crudActionService->deleteAction($request, $localRepository, $local, $successMsg, 'app_local_index', ['floor' => $floor->getId()]);
    }

    #[Route('/wall/{floor}/{area}', name: 'app_local_wall', methods: ['GET'])]
    public function wall(Request $request, LocalRepository $localRepository, Floor $floor, int $area): Response
    {
        $automaticWall = Local::createAutomaticWall($area);

        $floor->addLocal($automaticWall);
        $localRepository->save($floor, true);

        $this->addFlash('success', 'Se a creado el área de muro del área restante.');
        return new RedirectResponse($this->generateUrl('app_local_index', ['floor'=>$floor->getId()]), Response::HTTP_SEE_OTHER);
    }
}
