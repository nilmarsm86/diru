<?php

namespace App\Controller;

use App\Entity\Building;
use App\Entity\Enums\BuildingState;
use App\Entity\Floor;
use App\Entity\Role;
use App\Form\ExtraFloorForReplyType;
use App\Service\BuildingResetService;
use App\Service\BuildingStateService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(Role::ROLE_DRAFTSMAN)]
#[Route('/building/state')]
final class BuildingStateController extends AbstractController
{
    #[Route('/more_floors/{id}', name: 'app_building_state_more_floors', methods: ['GET'])]
    public function moreFloors(Building $building): Response
    {
        $form = $this->createForm(ExtraFloorForReplyType::class, null, [
            'action' => $this->generateUrl('app_building_state_reply', ['id' => $building->getId()]),
        ]);

        return $this->render('building_state/more_floors.html.twig', [
            'building' => $building,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/reply/{id}', name: 'app_building_state_reply', methods: ['POST'])]
    public function reply(Request $request, Building $building, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ExtraFloorForReplyType::class, null, [
            'action' => $this->generateUrl('app_building_state_reply', ['id' => $building->getId()]),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var int $floor */
            $floor = $form->get('floor')->getData();

            try {
                $building->reply($entityManager);
                if ($floor > 0) {
                    $lastPosition = 0;
                    if ($building->getFloors()->count() > 0) {
                        $floors = $building->getFloors();
                        /** @var Floor $last */
                        $last = $floors->last();
                        $lastPosition = $last->getPosition() ?? 0;
                    }

                    for ($i = $lastPosition + 1; $i <= ($floor + $lastPosition); ++$i) {
                        $building->createAutomaticFloor('Planta '.$i, false, $i, true, $entityManager);
                    }
                    $entityManager->persist($building);
                    $entityManager->flush();
                }
                $this->addFlash('success', 'Se ha replicado la obra');
            } catch (\Exception $exception) {
                $this->addFlash('error', $exception->getMessage());
            }

            return $this->redirectToRoute('app_floor_index', ['building' => $building->getId(), 'reply' => true]);
        }

        return $this->redirectToRoute('app_building_edit', ['id' => $building->getId(), 'project' => $building->getProject()?->getId()]);
    }

    #[Route('/state_transition/{type}/{id}', name: 'app_building_state_transition', methods: ['GET'])]
    public function stateTransition(Building $building, BuildingStateService $buildingStateService, string $type): Response
    {
        try {
            $buildingState = match ($type) {
                BuildingState::Revision->value => $buildingStateService->review($building),
                BuildingState::Design->value => $buildingStateService->design($building),
                BuildingState::Revised->value => $buildingStateService->revised($building),
                default => throw new \Exception('Transición no permitida.'),
            };

            $this->addFlash('success', 'Se ha pasado a estado de '.BuildingState::getLabelFrom($buildingState));
        } catch (\Exception $exception) {
            $this->addFlash('danger', $exception->getMessage());
        }

        return $this->redirectToRoute('app_building_edit', [
            'id' => $building->getId(),
            'project' => $building->getProject()?->getId(),
        ]);
    }

//    // TODO: agrupar en un solo metodo el cambio de estado y en dependencia del tipo de estado se gestionan sus revisiones
//    #[Route('/review/{id}', name: 'app_building_state_review', methods: ['GET'])]
//    public function review(Building $building, BuildingStateService $buildingStateService): Response
//    {
//        try {
//            $buildingStateService->review($building);
//            $this->addFlash('success', 'Se ha pasado a estado de '.BuildingState::Revision->getLabelFrom(BuildingState::Revision));
//        } catch (\Exception $exception) {
//            $this->addFlash('danger', $exception->getMessage());
//        }
//
//        return $this->redirectToRoute('app_building_edit', [
//            'id' => $building->getId(),
//            'project' => $building->getProject()?->getId(),
//        ]);
//    }

//    // TODO: agrupar en un solo metodo el cambio de estado y en dependencia del tipo de estado se gestionan sus revisiones
//    #[Route('/design/{id}', name: 'app_building_state_design', methods: ['GET'])]
//    public function design(Building $building, BuildingStateService $buildingStateService): Response
//    {
//        try {
//            $buildingStateService->design($building);
//            $this->addFlash('success', 'Se ha pasado a estado de diseño');
//        } catch (\Exception $exception) {
//            $this->addFlash('error', $exception->getMessage());
//        }
//
//        return $this->redirectToRoute('app_building_edit', [
//            'id' => $building->getId(),
//            'project' => $building->getProject()?->getId(),
//        ]);
//    }

//    // TODO: agrupar en un solo metodo el cambio de estado y en dependencia del tipo de estado se gestionan sus revisiones
//    #[Route('/revised/{id}', name: 'app_building_state_revised', methods: ['GET'])]
//    public function revised(Building $building, BuildingStateService $buildingStateService): Response
//    {
//        try {
//            $buildingStateService->revised($building);
//            $this->addFlash('success', 'Se ha pasado a estado de revisado.');
//        } catch (\Exception $exception) {
//            $this->addFlash('error', $exception->getMessage());
//        }
//
//        return $this->redirectToRoute('app_building_edit', [
//            'id' => $building->getId(),
//            'project' => $building->getProject()?->getId(),
//        ]);
//    }

    #[Route('/reset/{id}', name: 'app_building_state_reset', methods: ['GET'])]
    public function reset(Building $building, BuildingResetService $buildingResetService): Response
    {
        try {
            $buildingResetService->reset($building);
            $this->addFlash('success', 'Se ha reseteado la obra.');
        } catch (\Exception $exception) {
            $this->addFlash('error', 'Ha ocurrido un error al resetear la obra');
        }

        return $this->redirectToRoute('app_building_edit', [
            'id' => $building->getId(),
            'project' => $building->getProject()?->getId(),
        ]);
    }
}
