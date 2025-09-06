<?php

namespace App\Entity\Traits;

use App\Entity\Enums\TechnicalStatus;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

trait TechnicalStatusTrait
 {
    #[ORM\Column(length: 255)]
    private ?string $technicalStatus = null;

    #[Assert\Choice(
        choices: TechnicalStatus::CHOICES,
        message: 'Seleccione el estado técnico.'
    )]
    private TechnicalStatus $enumTechnicalStatus;

    public function getTechnicalStatus(): TechnicalStatus
    {
        return $this->enumTechnicalStatus;
    }

    public function setTechnicalStatus(TechnicalStatus $enumTechnicalStatus): static
    {
        $this->technicalStatus = '';
        $this->enumTechnicalStatus = $enumTechnicalStatus;

        return $this;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function onSaveTechnicalStatus(): void
    {
        $this->technicalStatus = $this->getTechnicalStatus()->value;
    }

    #[ORM\PostLoad]
    public function onLoadTechnicalStatus(): void
    {
        $this->setTechnicalStatus(TechnicalStatus::from($this->technicalStatus));
    }
 }