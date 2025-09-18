<?php

namespace App\Entity;

use App\Repository\UrbanRegulationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UrbanRegulationRepository::class)]
class UrbanRegulation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $code = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $data = null;

    #[ORM\Column(length: 255)]
    private ?string $MeasurementUnit = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $photo = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $comment = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $legalReference = null;

    #[ORM\ManyToOne(inversedBy: 'urbanRegulations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?UrbanRegulationType $type = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getData(): ?string
    {
        return $this->data;
    }

    public function setData(string $data): static
    {
        $this->data = $data;

        return $this;
    }

    public function getMeasurementUnit(): ?string
    {
        return $this->MeasurementUnit;
    }

    public function setMeasurementUnit(string $MeasurementUnit): static
    {
        $this->MeasurementUnit = $MeasurementUnit;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): static
    {
        $this->photo = $photo;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): static
    {
        $this->comment = $comment;

        return $this;
    }

    public function getLegalReference(): ?string
    {
        return $this->legalReference;
    }

    public function setLegalReference(string $legalReference): static
    {
        $this->legalReference = $legalReference;

        return $this;
    }

    public function getType(): ?UrbanRegulationType
    {
        return $this->type;
    }

    public function setType(?UrbanRegulationType $type): static
    {
        $this->type = $type;

        return $this;
    }
}
