<?php

namespace App\Controller;

use App\Controller\Traits\PdfResponseTrait;
use App\DTO\Paginator;
use App\Entity\Local;
use App\Entity\Role;
use App\Entity\SubSystem;
use App\Repository\ConstructiveActionRepository;
use App\Repository\LocalRepository;
use App\Service\CrudActionService;
use App\Service\Pdf\PdfAssetManager;
use App\Service\Pdf\PdfGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

#[IsGranted(Role::ROLE_DRAFTSMAN)]
#[Route('/local')]
final class LocalController extends AbstractController
{
    use PdfResponseTrait;

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
        $area = 1;
        $automaticWall = Local::createAutomaticWall($subSystem, $area, (int) $subSystem->getMaxLocalNumber() + 1, $reply, $entityManager);
        $localRepository->save($automaticWall, true);

        $this->addFlash('success', 'Se ha creado el área de muro del área restante.');

        return $this->redirectToRoute('app_local_index', ['subSystem' => $subSystem->getId(), 'reply' => $reply], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{subSystem}/resume/local', name: 'app_local_resume', methods: ['GET'])]
    public function resumeLocal(SubSystem $subSystem, ConstructiveActionRepository $constructiveActionRepository): Response
    {
        $ca = $this->constructiveActionStatus($subSystem, $constructiveActionRepository);

        return $this->render('local/resume.html.twig', [
            'local_status' => $subSystem->getAmountTechnicalStatus(),
            'meter_status' => $subSystem->getAmountMeterTechnicalStatus(),
            'constructive_action' => $ca,
            'title' => 'Estado técnico de los locales del subsistema',
            'sub_system' => $subSystem,
        ]);
    }

    #[Route('/{id}/local/technical_status', name: 'app_sub_system_report_local_technical_status', methods: ['GET'])]
    public function localTechnicalStatus(SubSystem $subSystem, PdfAssetManager $pdfAssetManager, PdfGenerator $pdfGenerator): Response
    {
        $html = $this->renderView('local/pdf/technical_status.html.twig', [
            'local_status' => $subSystem->getAmountTechnicalStatus(),
            'meter_status' => $subSystem->getAmountMeterTechnicalStatus(),
            'sub_system' => $subSystem,
            'logo' => $pdfAssetManager->getLogoBase64(),
        ]);

        $pdfContent = $pdfGenerator->generate($html);

        return $this->pdfResponse($pdfContent, 'estado_tecnico');
    }

    #[Route('/{id}/local/constructive_action', name: 'app_sub_system_report_local_constructive_action', methods: ['GET'])]
    public function localConstructiveAction(SubSystem $subSystem, ConstructiveActionRepository $constructiveActionRepository, PdfAssetManager $pdfAssetManager, PdfGenerator $pdfGenerator): Response
    {
        $ca = $this->constructiveActionStatus($subSystem, $constructiveActionRepository);

        $html = $this->renderView('local/pdf/constructive_actions.html.twig', [
            'constructive_action' => $ca,
            'sub_system' => $subSystem,
            'logo' => $pdfAssetManager->getLogoBase64(),
        ]);

        $pdfContent = $pdfGenerator->generate($html);

        return $this->pdfResponse($pdfContent, 'acciones_constructivas');
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function constructiveActionStatus(SubSystem $subSystem, ConstructiveActionRepository $constructiveActionRepository): array
    {
        $constructiveActionStatus = $subSystem->getAmountConstructiveAction();
        $constructiveActionPrice = $subSystem->getPriceByConstructiveAction();
        $constructiveActionMeter = $subSystem->getMeterByConstructiveAction();
        $constructiveActions = $constructiveActionRepository->findAll();
        $ca = [];

        foreach ($constructiveActions as $constructiveAction) {
            if (!array_key_exists($constructiveAction->getName(), $constructiveActionMeter)) {
                $ca[$constructiveAction->getName()] = [
                    'status' => 0,
                    'price' => 0,
                    'meter' => 0,
                ];
            } else {
                $ca[$constructiveAction->getName()] = [
                    'status' => $constructiveActionStatus[$constructiveAction->getName()],
                    'price' => $constructiveActionPrice[$constructiveAction->getName()],
                    'meter' => $constructiveActionMeter[$constructiveAction->getName()],
                ];
            }
        }

        return $ca;
    }
}
