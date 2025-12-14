<?php

namespace App\Controller;

use App\DTO\Paginator;
use App\Entity\Local;
use App\Entity\SubSystem;
use App\Repository\LocalRepository;
use App\Service\CrudActionService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\RouterInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

#[Route('/local')]
final class LocalController extends AbstractController
{
    #[Route('/{subSystem}/{reply}', name: 'app_local_index', requirements: ['subSystem' => '\d+'], methods: ['GET'])]
    public function index(Request $request, RouterInterface $router, LocalRepository $localRepository, SubSystem $subSystem, bool $reply = false): Response
    {
        $filter = $request->query->get('filter', '');
        $amountPerPage = (int) $request->query->get('amount', '10');
        $pageNumber = (int) $request->query->get('page', '1');

        $data = $localRepository->findSubSystemLocals($subSystem, $filter, $amountPerPage, $pageNumber, $reply);

        $paginator = new Paginator($data, $amountPerPage, $pageNumber);
        if ($paginator->isFromGreaterThanTotal()) {
            return $paginator->greatherThanTotal($request, $router, $pageNumber);
        }

        $template = ($request->isXmlHttpRequest()) ? '_list.html.twig' : 'index.html.twig';

        return $this->render("local/$template", [
            'filter' => $filter,
            'paginator' => $paginator,
            'sub_system' => $subSystem,
            'reply' => $reply,
        ]);
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    #[Route('/new/{subSystem}/{reply}', name: 'app_local_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CrudActionService $crudActionService, SubSystem $subSystem, bool $reply = false): Response
    {
        $local = new Local();

        return $crudActionService->formLiveComponentAction($request, $local, 'local', [
            'title' => 'Nuevo Local',
            'sub_system' => $subSystem,
            'reply' => $reply,
            'local' => $local,
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
    #[Route('/{id}/edit/{subSystem}/{reply}', name: 'app_local_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Local $local, CrudActionService $crudActionService, SubSystem $subSystem, bool $reply = false): Response
    {
        return $crudActionService->formLiveComponentAction($request, $local, 'local', [
            'title' => 'Editar Local',
            'sub_system' => $subSystem,
            'reply' => $reply,
        ]);
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    #[Route('/{id}/{subSystem}', name: 'app_local_delete', methods: ['POST'])]
    public function delete(Request $request, Local $local, LocalRepository $localRepository, CrudActionService $crudActionService, SubSystem $subSystem): Response
    {
        $successMsg = 'Se ha eliminado el local.';
        $response = $crudActionService->deleteAction($request, $localRepository, $local, $successMsg, 'app_local_index', [
            'subSystem' => $subSystem->getId(),
        ]);
        if ($response instanceof RedirectResponse) {
            $this->addFlash('success', $successMsg);

            return $response;
        }

        return $response;
    }

    #[Route('/wall/{subSystem}/{reply}', name: 'app_local_wall', methods: ['GET'])]
    public function wall(EntityManagerInterface $entityManager, LocalRepository $localRepository, SubSystem $subSystem, bool $reply = false): Response
    {
        $area = 0;
        if (true === $subSystem->getFloor()?->hasFreeArea()) {
            $area = (float) $subSystem->getFloor()->getFreeArea();
        }

        if (true === $subSystem->getFloor()?->hasUnassignedArea()) {
            $area = (float) $subSystem->getFloor()->getUnassignedArea();
        }

        $automaticWall = Local::createAutomaticWall($subSystem, $area, $subSystem->getMaxLocalNumber() + 1, $reply, $entityManager);

        $localRepository->save($automaticWall, true);

        $this->addFlash('success', 'Se ha creado el área de muro del área restante.');

        return $this->redirectToRoute('app_local_index', ['subSystem' => $subSystem->getId(), 'reply' => $reply], Response::HTTP_SEE_OTHER);
    }
}
