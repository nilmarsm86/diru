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
    #[Route('/building/separate/concept/{id}', name: 'app_building_separate_concept')]
    public function save(Request $request, BuildingSeparateConcept $buildingSeparateConcept, BuildingSeparateConceptRepository $buildingSeparateConceptRepository): Response
    {
        $buildingSeparateConcept->setPercent((float) $request->request->get('percent', 0));

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
