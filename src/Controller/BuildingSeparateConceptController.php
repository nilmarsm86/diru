<?php

namespace App\Controller;

use App\Entity\Building;
use App\Entity\BuildingSeparateConcept;
use App\Entity\Role;
use App\Repository\BuildingRepository;
use App\Repository\BuildingSeparateConceptRepository;
use App\Repository\SeparateConceptRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(Role::ROLE_DRAFTSMAN)]
final class BuildingSeparateConceptController extends AbstractController
{
    #[Route('/building/separate/concept/estimate/{id}', name: 'app_building_separate_concept_estimate')]
    public function estimate(Request $request, BuildingSeparateConcept $buildingSeparateConcept, BuildingSeparateConceptRepository $buildingSeparateConceptRepository): Response
    {
        $buildingSeparateConcept->setPercentEstimatedAdjustValue((float) $request->request->get('percent', 0));

        $buildingSeparateConceptRepository->save($buildingSeparateConcept, true);

        return $this->render('partials/_form_success.html.twig', [
            'id' => 'new_'.$this->getClassName(BuildingSeparateConcept::class).'_'.$buildingSeparateConcept->getId().'_'.time(),
            'type' => 'text-bg-success',
            'message' => 'Se a actualizado el desglose.',
        ]);
    }

    #[Route('/building/separate/concept/estimate_reset/{id}', name: 'app_building_separate_concept_estimate_reset')]
    public function estimateReset(SeparateConceptRepository $separateConceptRepository, Building $building, BuildingRepository $buildingRepository): Response
    {
        $this->addSeparateConcepts($separateConceptRepository, $building);

        $buildingRepository->save($building, true);

        return $this->render('partials/_form_success.html.twig', [
            'id' => 'new_'.$this->getClassName(Building::class).'_'.$building->getId().'_'.time(),
            'type' => 'text-bg-success',
            'message' => 'Se a reseteado el desglose.',
        ]);
    }

    #[Route('/building/separate/concept/execute/{id}', name: 'app_building_separate_concept_execute')]
    public function execute(Request $request, BuildingSeparateConcept $buildingSeparateConcept, BuildingSeparateConceptRepository $buildingSeparateConceptRepository): Response
    {
        $buildingSeparateConcept->setPercentEstimatedToExecuteValue((float) $request->request->get('percent', 0));

        $buildingSeparateConceptRepository->save($buildingSeparateConcept, true);

        return $this->render('partials/_form_success.html.twig', [
            'id' => 'new_'.$this->getClassName(BuildingSeparateConcept::class).'_'.$buildingSeparateConcept->getId().'_'.time(),
            'type' => 'text-bg-success',
            'message' => 'Se a actualizado el desglose.',
        ]);
    }

    #[Route('/building/separate/concept/real/{id}', name: 'app_building_separate_concept_real')]
    public function real(Request $request, BuildingSeparateConcept $buildingSeparateConcept, BuildingSeparateConceptRepository $buildingSeparateConceptRepository): Response
    {
        $buildingSeparateConcept->setPercentRealValue((float) $request->request->get('percent', 0));

        $buildingSeparateConceptRepository->save($buildingSeparateConcept, true);

        return $this->render('partials/_form_success.html.twig', [
            'id' => 'new_'.$this->getClassName(BuildingSeparateConcept::class).'_'.$buildingSeparateConcept->getId().'_'.time(),
            'type' => 'text-bg-success',
            'message' => 'Se a actualizado el desglose.',
        ]);
    }

    private function getClassName(string $classname): string
    {
        $pos = strrpos($classname, '\\');

        if (false !== $pos) {
            return substr($classname, $pos + 1);
        }

        return $classname;
    }

    private function addSeparateConcepts(SeparateConceptRepository $separateConceptRepository, Building $building): void
    {
        $existingSeparateConcepts = $building->getBuildingSeparateConcepts();

        $separateConcepts = $separateConceptRepository->findBy([], ['number' => 'ASC']);
        foreach ($separateConcepts as $separateConcept) {
            foreach ($existingSeparateConcepts as $existingSeparateConcept) {
                if ($existingSeparateConcept->getSeparateConcept()?->getId() === $separateConcept->getId()) {
                    $existingSeparateConcept->setPercentEstimatedAdjustValue($separateConcept->getPercent() ?? 0);
                    continue;
                }
            }

            // solo agregar cuando no existe
            $percent = (bool) $separateConcept->getPercent() ? $separateConcept->getPercent() : 0;

            $buildingSeparateConcept = new BuildingSeparateConcept();
            $buildingSeparateConcept->setBuilding($building);
            $buildingSeparateConcept->setSeparateConcept($separateConcept);
            $buildingSeparateConcept->setPercentEstimatedAdjustValue($percent);
            $buildingSeparateConcept->setPercentEstimatedToExecuteValue($percent);
            $buildingSeparateConcept->setPercentRealValue($percent);

            $building->addBuildingSeparateConcept($buildingSeparateConcept);
        }
    }
}
