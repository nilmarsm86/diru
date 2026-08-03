<?php

namespace App\Controller;

use App\Controller\Traits\PdfResponseTrait;
use App\DTO\Paginator;
use App\Entity\Floor;
use App\Entity\Role;
use App\Entity\SubSystem;
use App\Repository\SubSystemRepository;
use App\Service\CrudActionService;
use App\Service\Pdf\PdfAssetManager;
use App\Service\Pdf\PdfGenerator;
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
#[Route('/sub/system')]
final class SubSystemController extends AbstractController
{
    use PdfResponseTrait;

    #[Route('/{floor}/{reply}', name: 'app_sub_system_index', requirements: ['floor' => '\d+'], methods: ['GET'])]
    public function index(Request $request, RouterInterface $router, CrudActionService $crudActionService, SubSystemRepository $subSystemRepository, Floor $floor, bool $reply = false): Response
    {
        /** @var array{string, int, int} $result */
        $result = $crudActionService->getManageQuerys($request);
        list($filter, $amountPerPage, $pageNumber) = $result;

        $data = $subSystemRepository->findSubsystemsFloor($floor, $filter, $amountPerPage, $pageNumber, $reply);

        $paginator = new Paginator($data, $amountPerPage, $pageNumber);
        if ($paginator->isFromGreaterThanTotal()) {
            return $paginator->greatherThanTotal($request, $router, $pageNumber);
        }

        $template = ($request->isXmlHttpRequest()) ? '_list.html.twig' : 'index.html.twig';

        return $this->render("sub_system/$template", [
            'filter' => $filter,
            'paginator' => $paginator,
            'floor' => $floor,
            'reply' => $reply,
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/new/{floor}/{reply}', name: 'app_sub_system_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CrudActionService $crudActionService, Floor $floor, bool $reply = false): Response
    {
        $subSystem = new SubSystem();

        return $crudActionService->formLiveComponentAction($request, $subSystem, 'sub_system', [
            'title' => 'Nuevo Subsistema',
            'floor' => $floor,
            'reply' => $reply,
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/{id}', name: 'app_sub_system_show', methods: ['GET'])]
    public function show(Request $request, SubSystem $subSystem, CrudActionService $crudActionService): Response
    {
        return $crudActionService->showAction($request, $subSystem, 'sub_system', 'subSystem', 'Detalles del subsistema');
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    #[Route('/{id}/edit/{floor}/{reply}', name: 'app_sub_system_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, SubSystem $subSystem, CrudActionService $crudActionService, Floor $floor, bool $reply = false): Response
    {
        return $crudActionService->formLiveComponentAction($request, $subSystem, 'sub_system', [
            'title' => 'Editar Subsistema',
            'floor' => $floor,
            'reply' => $reply,
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/{id}', name: 'app_sub_system_delete', methods: ['POST'])]
    public function delete(Request $request, SubSystem $subSystem, SubSystemRepository $subSystemRepository, CrudActionService $crudActionService, Floor $floor): Response
    {
        $successMsg = 'Se ha eliminado el subsistema.';
        $response = $crudActionService->deleteAction($request, $subSystemRepository, $subSystem, $successMsg, 'app_sub_system_index', [
            'floor' => $floor->getId(),
        ]);
        if ($response instanceof RedirectResponse) {
            $this->addFlash('success', $successMsg);

            return $response;
        }

        return $response;
    }

    #[Route('/{floor}/resume/subsystem', name: 'app_sub_system_resume', methods: ['GET'])]
    public function resumeSubSystem(Floor $floor): Response
    {
        return $this->render('sub_system/resume.html.twig', [
            //            'sub_system_status' => $floor->getAmountTechnicalStatus(),
            //            'meter_status' => $floor->getAmountMeterTechnicalStatus(),
            'classification' => $floor->getAmountByClassification(),
            'title' => 'Estado técnico de los subsistemas de la planta',
            'floor' => $floor,
        ]);
    }

    #[Route('/{floor}/sub_system/classification', name: 'app_sub_system_report_classification', methods: ['GET'])]
    public function subsystemClassification(Floor $floor, PdfAssetManager $pdfAssetManager, PdfGenerator $pdfGenerator): Response
    {
        $html = $this->renderView('sub_system/pdf/classification.html.twig', [
            'classification' => $floor->getAmountByClassification(),
            'floor' => $floor,
            'logo' => $pdfAssetManager->getLogoBase64(),
        ]);

        $pdfContent = $pdfGenerator->generate($html);

        return $this->pdfResponse($pdfContent, 'classification_subsistema.pdf');
    }
}
