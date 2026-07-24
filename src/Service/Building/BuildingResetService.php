<?php

namespace App\Service\Building;

use App\Entity\Building;
use App\Entity\Enums\BuildingState;
use App\Entity\Floor;
use App\Entity\SubSystem;
use Doctrine\ORM\EntityManagerInterface;

readonly class BuildingResetService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * Resetea completamente una obra nueva (solo se permite en obras marcadas como isNew).
     *
     * @throws \DomainException si la obra no puede ser reseteada
     */
    public function reset(Building $building): void
    {
        $this->validateCanReset($building);

        $this->entityManager->wrapInTransaction(function () use ($building) {
            $this->removeAllFloorsAndChildren($building);

            $land = $building->getLand();
            if (null !== $land) {
                $building->setLand(null);
                $this->entityManager->remove($land);
            }

            $building->setIsNew(null);
            //            $building->setHasReply(null);
            // Opcional: resetear otros campos a estado inicial si es necesario
            $building->setStopReason(null);
            $building->setEstimatedValueEquipment(0);
            $building->setEstimatedValueOther(0);
            $building->setApprovedValueConstruction(0);
            $building->setApprovedValueEquipment(0);
            $building->setApprovedValueOther(0);
            $this->removeAllDraftsman($building);
            //            $this->removeAllConstructor($building);
            $this->removeAllCorporateEntity($building);
            $building->setPopulation(1);
            $building->setConstructionAssembly(0);
            $building->setConstructionAssemblyComment(null);
            $this->removeAllLandNetworkConnection($building);
            $this->removeAllUrbanizationEstimates($building);
            $this->removeAllProjectTechnicalPreparationEstimates($building);
            $this->removeAllJustValueEstimate($building);
            $this->removeAllBuildingSeparateConcepts($building);
            $building->setCoefficient(0);
            $this->removeAllBuildingRevisions($building);
            $building->setConstructionRealValue(0);
            $building->setConstructionRealValueComment(null);

            //            $this->removeActivity($building);

            $building->setObjects([]);

            $building->setState(BuildingState::Registered);
            //             $building->setRegisterAt(new \DateTimeImmutable());

            $this->entityManager->persist($building);
        });

        // Opcional: dispatch event BuildingWasReset para notificar a otros sistemas
    }

    private function validateCanReset(Building $building): void
    {
        if (null === $building->isNew()) {  // Cambia null === $building->isNew() por una comprobación clara
            throw new \DomainException('Esta obra no puede ser reseteada. Solo las obras nuevas pueden resetearce.');
        }

        // Añade más guardias según reglas de negocio:
        // if ($building->hasActiveDraftsman()) { ... }
        // if ($building->hasReply()) { ... }
    }

    private function removeAllFloorsAndChildren(Building $building): void
    {
        // Copia para evitar modificación durante iteración
        $floors = $building->getFloors()->toArray();

        foreach ($floors as $floor) {
            $this->removeFloorAndChildren($floor);
            $building->removeFloor($floor);

            $this->entityManager->remove($floor);
        }
    }

    private function removeFloorAndChildren(Floor $floor): void
    {
        $subSystems = $floor->getSubSystems()->toArray();  // Asumiendo que tienes getSubSystems()

        foreach ($subSystems as $subSystem) {
            $this->removeSubSystemAndChildren($subSystem);
            $floor->removeSubSystem($subSystem);

            $this->entityManager->remove($subSystem);
        }
    }

    private function removeSubSystemAndChildren(SubSystem $subSystem): void
    {
        $locals = $subSystem->getLocals()->toArray();

        foreach ($locals as $local) {
            $subSystem->removeLocal($local);

            $this->entityManager->remove($local);
        }
    }

    private function removeAllDraftsman(Building $building): void
    {
        $draftsmans = $building->getDraftsmans()->toArray();

        foreach ($draftsmans as $draftsman) {
            $building->removeDraftsman($draftsman);
        }
    }

    //    private function removeAllConstructor(Building $building): void
    //    {
    //        $constructors = $building->getConstructors()->toArray();
    //
    //        foreach ($constructors as $constructor) {
    //            $building->removeConstructor($constructor);
    //        }
    //    }

    private function removeAllCorporateEntity(Building $building): void
    {
        $corporateEntities = $building->getConstructorCorporateEntities()->toArray();

        foreach ($corporateEntities as $corporateEntity) {
            $building->removeConstructorCorporateEntity($corporateEntity);
        }
    }

    private function removeAllLandNetworkConnection(Building $building): void
    {
        $landNetworkConnections = $building->getLandNetworkConnections()->toArray();

        foreach ($landNetworkConnections as $landNetworkConnection) {
            $building->removeLandNetworkConnection($landNetworkConnection);

            $this->entityManager->remove($landNetworkConnection);
        }
    }

    private function removeAllUrbanizationEstimates(Building $building): void
    {
        $urbanizationEstimates = $building->getUrbanizationEstimates()->toArray();

        foreach ($urbanizationEstimates as $urbanizationEstimate) {
            $building->removeUrbanizationEstimate($urbanizationEstimate);

            $this->entityManager->remove($urbanizationEstimate);
        }
    }

    private function removeAllProjectTechnicalPreparationEstimates(Building $building): void
    {
        $projectTechnicalPreparationEstimates = $building->getProjectTechnicalPreparationEstimates()->toArray();

        foreach ($projectTechnicalPreparationEstimates as $projectTechnicalPreparationEstimate) {
            $building->removeProjectTechnicalPreparationEstimate($projectTechnicalPreparationEstimate);

            $this->entityManager->remove($projectTechnicalPreparationEstimate);
        }
    }

    private function removeAllJustValueEstimate(Building $building): void
    {
        $justValueEstimates = $building->getJustValueEstimates()->toArray();

        foreach ($justValueEstimates as $justValueEstimate) {
            $building->removeJustValueEstimate($justValueEstimate);

            $this->entityManager->remove($justValueEstimate);
        }
    }

    private function removeAllBuildingSeparateConcepts(Building $building): void
    {
        $buildingSeparateConcepts = $building->getBuildingSeparateConcepts()->toArray();

        foreach ($buildingSeparateConcepts as $buildingSeparateConcept) {
            $building->removeBuildingSeparateConcept($buildingSeparateConcept);

            $this->entityManager->remove($buildingSeparateConcept);
        }
    }

    private function removeAllBuildingRevisions(Building $building): void
    {
        $buildingRevisions = $building->getBuildingRevisions()->toArray();

        foreach ($buildingRevisions as $buildingRevision) {
            $building->removeBuildingRevision($buildingRevision);

            $this->entityManager->remove($buildingRevision);
        }
    }

    public function removeActivity(Building $building): void
    {
        $activity = $building->getActivity();
        if (null !== $activity) {
            $building->setActivity(null);
            $this->entityManager->remove($activity);
        }
    }
}
