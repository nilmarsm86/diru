<?php

namespace App\Service\Building;

use App\Entity\Building;
use App\Entity\Enums\BuildingState;
use Doctrine\ORM\EntityManagerInterface;

readonly class BuildingStateService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * Cambia el estado de la obra con todas las reglas y side-effects necesarios.
     *
     * @throws \DomainException si la transición no está permitida
     */
    public function transitionTo(Building $building, BuildingState $newState): void
    {
        $currentState = $building->getState();

        if ($currentState === $newState) {
            return; // idempotente
        }

        $this->validateTransition($currentState, $newState);

        // Ejecutar side-effects según el nuevo estado
        $this->handleSideEffects($building, $newState);

        $building->setState($newState);

        // Actualizar timestamps si corresponde (mejor centralizar aquí que en la entidad)
        $this->updateStateTimestamps($building, $newState);

        $this->entityManager->persist($building);
        $this->entityManager->flush();
    }

    private function handleSideEffects(Building $building, BuildingState $newState): void
    {
        match ($newState) {
            BuildingState::RevisionDraftsman,
            BuildingState::RevisedDraftsman => $this->handleRevisedDraftmanTransition($building),
            BuildingState::Design => $this->handleDesignTransition($building),

            // Aquí irán futuros estados: Canceled, OnHold, Approved, etc.
            default => null,
        };
    }

    private function validateTransition(BuildingState $current, BuildingState $new): void
    {
        // Reglas de negocio claras y fáciles de mantener
        $allowedTransitions = [
            BuildingState::Registered->value => [BuildingState::Design->value],
            BuildingState::Design->value => [BuildingState::RevisionDraftsman->value],
            BuildingState::RevisionDraftsman->value => [BuildingState::RevisedDraftsman->value, BuildingState::Design->value],
            BuildingState::RevisedDraftsman->value => [BuildingState::RevisionInvestmen->value, BuildingState::Design->value],
            BuildingState::RevisionInvestmen->value => [BuildingState::RevisedInvestmen->value, BuildingState::Design->value],
            BuildingState::RevisedInvestmen->value => [BuildingState::RevisionDirector->value, BuildingState::Design->value],
            BuildingState::RevisionDirector->value => [BuildingState::RevisedDirector->value, BuildingState::Design->value],
            // Añade aquí más reglas según tu flujo real
        ];

        if (!isset($allowedTransitions[$current->value]) || !in_array($new->value, $allowedTransitions[$current->value], true)) {
            throw new \DomainException(sprintf('Transición no permitida de %s a %s en la obra.', BuildingState::getLabelFrom($current), BuildingState::getLabelFrom($new)));
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

    private function handleRevisedDraftmanTransition(Building $building): void
    {
        // TODO: revisar que todos los locales tengan acciones constructivas
        $this->deactivateActiveRevisions($building);
        // TODO: guardar el ITE de los subsistemas
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
            BuildingState::RevisionDraftsman => $building->setRevisionDraftsmanAt($now),
            BuildingState::RevisedDraftsman => $building->setRevisedDraftsmanAt($now),
            BuildingState::RevisionInvestmen => $building->setRevisionInvestmenAt($now),
            BuildingState::RevisedInvestmen => $building->setRevisedInvestmenAt($now),
            BuildingState::RevisionDirector => $building->setRevisionDirectorAt($now),
            BuildingState::RevisedDirector => $building->setRevisedDirectorAt($now),
            default => null,
        };
    }

    // Métodos públicos de conveniencia (mantienes API amigable)
    public function design(Building $building): BuildingState
    {
        $this->transitionTo($building, BuildingState::Design);

        return BuildingState::Design;
    }

    public function reviewDraftsman(Building $building): BuildingState
    {
        $this->transitionTo($building, BuildingState::RevisionDraftsman);

        return BuildingState::RevisionDraftsman;
    }

    public function revisedDraftsman(Building $building): BuildingState
    {
        $this->transitionTo($building, BuildingState::RevisedDraftsman);

        return BuildingState::RevisedDraftsman;
    }

    public function reviewInvestmen(Building $building): BuildingState
    {
        $this->transitionTo($building, BuildingState::RevisionInvestmen);

        return BuildingState::RevisionInvestmen;
    }

    public function revisedInvestmen(Building $building): BuildingState
    {
        $this->transitionTo($building, BuildingState::RevisedInvestmen);

        return BuildingState::RevisedInvestmen;
    }

    public function reviewDirector(Building $building): BuildingState
    {
        $this->transitionTo($building, BuildingState::RevisionDirector);

        return BuildingState::RevisionDirector;
    }

    public function revisedDirector(Building $building): BuildingState
    {
        $this->transitionTo($building, BuildingState::RevisedDirector);

        return BuildingState::RevisedDirector;
    }
}
