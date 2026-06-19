<?php

namespace App\Controller;

use App\Entity\Building;
use App\Entity\Role;
use App\Repository\BuildingSeparateConceptRepository;
use App\Repository\SeparateConceptRepository;
use App\Service\AssociativeEntryCollection;
use App\Service\Building\BuildingValuationService;
use App\Service\FormulaEvaluator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(Role::ROLE_DRAFTSMAN)]
#[Route('/building/report')]
final class BuildingReportController extends AbstractController
{
    #[Route('/{id}/report/local', name: 'app_building_report_local', methods: ['GET'])]
    public function reportLocal(Building $building): Response
    {
        return $this->render('building_report/report.html.twig', [
            'local_status' => $building->getAmountTechnicalStatus(),
            'meter_status' => $building->getAmountMeterTechnicalStatus(),
            'title' => 'Estado técnico de los locales de la obra',
            'building' => $building,
        ]);
    }

    #[Route('/separate/presupposition/estimate/{id}', name: 'app_building_report_separate_presupposition_estimate', methods: ['GET'])]
    public function separatePresuppositionEstimate(
        Request $request,
        Building $building,
        BuildingSeparateConceptRepository $buildingSeparateConceptRepository,
        SeparateConceptRepository $separateConceptRepository,
        FormulaEvaluator $formulaEvaluator,
        BuildingValuationService $buildingValuationService,
    ): Response {
        $template = ($request->isXmlHttpRequest()) ? 'presupposition_estimate.html.twig' : 'separate_presupposition.html.twig';

        return $this->render("building_report/$template", [
            'building' => $building,
            'project' => $building->getProject()?->getId(),
            'buildingSeparateConceptRepository' => $buildingSeparateConceptRepository,
            'separateConceptRepository' => $separateConceptRepository,
            'aec' => AssociativeEntryCollection::empty(),
            'formulaEvaluator' => $formulaEvaluator,
            'buildingValuationService' => $buildingValuationService,
        ]);
    }

    #[Route('/separate/presupposition/execute/{id}', name: 'app_building_report_separate_presupposition_execute', methods: ['GET'])]
    public function separatePresuppositionExecute(
        Request $request,
        Building $building,
        BuildingSeparateConceptRepository $buildingSeparateConceptRepository,
        SeparateConceptRepository $separateConceptRepository,
        FormulaEvaluator $formulaEvaluator,
        BuildingValuationService $buildingValuationService,
    ): Response {
        $template = ($request->isXmlHttpRequest()) ? 'presupposition_execute.html.twig' : 'separate_presupposition.html.twig';

        return $this->render("building_report/$template", [
            'building' => $building,
            'project' => $building->getProject()?->getId(),
            'buildingSeparateConceptRepository' => $buildingSeparateConceptRepository,
            'separateConceptRepository' => $separateConceptRepository,
            'aec' => AssociativeEntryCollection::empty(),
            'formulaEvaluator' => $formulaEvaluator,
            'buildingValuationService' => $buildingValuationService,
        ]);
    }

    #[Route('/separate/presupposition/real/{id}', name: 'app_building_report_separate_presupposition_real', methods: ['GET'])]
    public function separatePresuppositionReal(
        Request $request,
        Building $building,
        BuildingSeparateConceptRepository $buildingSeparateConceptRepository,
        SeparateConceptRepository $separateConceptRepository,
        FormulaEvaluator $formulaEvaluator,
        BuildingValuationService $buildingValuationService,
    ): Response {
        $template = ($request->isXmlHttpRequest()) ? 'presupposition_real.html.twig' : 'separate_presupposition.html.twig';

        return $this->render("building_report/$template", [
            'building' => $building,
            'project' => $building->getProject()?->getId(),
            'buildingSeparateConceptRepository' => $buildingSeparateConceptRepository,
            'separateConceptRepository' => $separateConceptRepository,
            'aec' => AssociativeEntryCollection::empty(),
            'formulaEvaluator' => $formulaEvaluator,
            'buildingValuationService' => $buildingValuationService,
        ]);
    }
}
