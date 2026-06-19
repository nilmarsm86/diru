<?php

namespace App\Controller;

use App\Entity\BuildingSeparateConcept;
use App\Entity\Role;
use App\Repository\BuildingSeparateConceptRepository;
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
}
