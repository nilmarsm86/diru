<?php

namespace App\Service;

use App\Entity\Building;
use App\Entity\Enums\BuildingState;
use Doctrine\ORM\EntityManagerInterface;
use DomainException;

readonly class BuildingStateService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * Cambia el estado de la obra con todas las reglas y side-effects necesarios.
     *
     * @throws DomainException si la transición no está permitida
     */
    public function transitionTo(Building $building, BuildingState $newState): void
    {
        $currentState = $building->getState();

        if ($currentState === $newState) {
            return; // idempotente
        }

        $this->validateTransition($currentState, $newState);

        // Ejecutar side-effects según el nuevo estado
        match ($newState) {
            BuildingState::Revision,
            BuildingState::Revised => function () use ($building) {
                //TODO: revisar que todos los locales tengan acciones constructivas
                $this->deactivateActiveRevisions($building);
                //TODO: guardar el ITE de los subsistemas
            },
            BuildingState::Design => $this->handleDesignTransition($building),

            // Aquí irán futuros estados: Canceled, OnHold, Approved, etc.
            default => null,
        };

        $building->setState($newState);

        // Actualizar timestamps si corresponde (mejor centralizar aquí que en la entidad)
        $this->updateStateTimestamps($building, $newState);

        $this->entityManager->persist($building);
        $this->entityManager->flush();
    }

    private function validateTransition(BuildingState $current, BuildingState $new): void
    {
        // Reglas de negocio claras y fáciles de mantener
        $allowedTransitions = [
            BuildingState::Registered->value => [BuildingState::Design->value/* , BuildingState::Revision->value */],
            BuildingState::Design->value => [BuildingState::Revision->value/*, BuildingState::Revised->value*/],
            BuildingState::Revision->value => [BuildingState::Revised->value, BuildingState::Design->value],
//            BuildingState::Revised->value => [BuildingState::Design->value, BuildingState::Revision->value],
            // Añade aquí más reglas según tu flujo real
        ];

        if (!isset($allowedTransitions[$current->value]) || !in_array($new->value, $allowedTransitions[$current->value], true)) {
            throw new DomainException(sprintf('Transición no permitida de %s a %s en la obra.', $current->getLabelFrom($current), $new->getLabelFrom($new)));
        }
    }

    private function deactivateActiveRevisions(Building $building): void
    {
        foreach ($building->getBuildingRevisions() as $revision) {
            if ($revision->isActive()) {
                $revision->deactivate();
                $this->entityManager->persist($revision);
            }
        }
    }

    private function handleDesignTransition(Building $building): void
    {
        // Aquí puedes poner lógica específica de cuando pasa a Diseño
        // Ej: limpiar algo, crear revisiones iniciales, etc.
    }

    private function updateStateTimestamps(Building $building, BuildingState $state): void
    {
        $now = new \DateTimeImmutable();

        match ($state) {
            BuildingState::Registered => $building->setRegisterAt($now),
            BuildingState::Design => $building->setDesignAt($now),
            BuildingState::Revision => $building->setRevisionAt($now),
            BuildingState::Revised => $building->setRevisedAt($now),
            default => null,
        };
    }

    // Métodos públicos de conveniencia (mantienes API amigable)
    public function review(Building $building): BuildingState
    {
        $this->transitionTo($building, BuildingState::Revision);

        return BuildingState::Revision;
    }

    public function design(Building $building): BuildingState
    {
        $this->transitionTo($building, BuildingState::Design);

        return BuildingState::Design;
    }

    public function revised(Building $building): BuildingState
    {
        $this->transitionTo($building, BuildingState::Revised);

        return BuildingState::Revised;
    }
}
