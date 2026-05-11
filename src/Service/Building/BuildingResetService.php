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
            // Opcional: resetear otros campos a estado inicial si es necesario
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
}
