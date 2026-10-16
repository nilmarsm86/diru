<?php

namespace App\Service\Structure;

use App\Entity\Local;

class LocalChange
{
    /**
     * @param array<string> $changesFromOriginal
     */
    public function __construct(private array $changesFromOriginal)
    {
    }

    public function hasChangesFromOriginal(Local $original, Local $reply): bool
    {
        $hasChanges = false;
        $this->changesFromOriginal = [];

        $this->changeInNumber($original, $reply);
        $this->changeInArea($original, $reply);
        $this->changeInType($original, $reply);
        $this->changeInHeight($original, $reply);
        $this->changeInVolume($original, $reply);
        $this->changeInTechnicalStatus($original, $reply);
        $this->changeInLevels($original, $reply);
        $this->changeFromRemoveLocal($original, $reply);

        if (count($this->changesFromOriginal) > 1) {
            $hasChanges = true;
        }

        return $hasChanges;
    }

    private function changeInNumber(Local $original, Local $reply): void
    {
        if ($original->getNumber() !== $reply->getNumber()) {
            $this->changesFromOriginal[] = 'Cambio de número.';
        }
    }

    private function changeInArea(Local $original, Local $reply): void
    {
        if ($original->getArea() !== $reply->getArea()) {
            $this->changesFromOriginal[] = 'Cambio de área.';
        }
    }

    private function changeInType(Local $original, Local $reply): void
    {
        if ($original->getType() !== $reply->getType()) {
            $this->changesFromOriginal[] = 'Cambio de tipo de estructura.';
        }
    }

    private function changeInHeight(Local $original, Local $reply): void
    {
        if ($original->getHeight() !== $reply->getHeight()) {
            $this->changesFromOriginal[] = 'Cambio de altura.';
        }
    }

    private function changeInVolume(Local $original, Local $reply): void
    {
        if ($original->getVolume() !== $reply->getVolume()) {
            $this->changesFromOriginal[] = 'Cambio de volumen.';
        }
    }

    private function changeInTechnicalStatus(Local $original, Local $reply): void
    {
        if ($original->getTechnicalStatus() !== $reply->getTechnicalStatus()) {
            $this->changesFromOriginal[] = 'Cambio de estado técnico.';
        }
    }

    private function changeInLevels(Local $original, Local $reply): void
    {
        if ($original->isImpactHigherLevels() !== $reply->isImpactHigherLevels()) {
            $this->changesFromOriginal[] = 'Cambio en el impacto en niveles superiores.';
        }
    }

    private function changeFromRemoveLocal(Local $original, Local $reply): void
    {
        if ((false === $original->hasRemoveConstructiveAction()) && (true === $reply->hasRemoveConstructiveAction())) {
            $this->changesFromOriginal[] = 'El local ha sido removido.';
        }
    }
}
