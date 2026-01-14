<?php

namespace App\Entity;

use App\Entity\Enums\UrbanRegulationStructure;
use App\Repository\UrbanRegulationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UrbanRegulationRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[DoctrineAssert\UniqueEntity(fields: ['code'], message: 'Ya existe una regulación urbana con este código.')]
class UrbanRegulation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'El código de la regulación está vacío.')]
    private ?string $code = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: 'El descripción de la regulación está vacía.')]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'El dato de la regulación está vacío.')]
    private ?string $data = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'La unidad de medida de la regulación está vacía.')]
    private ?string $measurementUnit = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $photo = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $comment = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $legalReference = null;

    #[ORM\ManyToOne(inversedBy: 'urbanRegulations')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank(message: 'Seleccione o cree el tipo de regulación.')]
    #[Assert\Valid]
    private ?UrbanRegulationType $type = null;

    /**
     * @var Collection<int, ProjectUrbanRegulation>
     */
    #[ORM\OneToMany(targetEntity: ProjectUrbanRegulation::class, mappedBy: 'urbanRegulation', cascade: ['persist'])]
    #[Assert\Valid]
    private Collection $projectUrbanRegulations;

    #[ORM\Column(length: 255)]
    private string $structure;

    #[Assert\Choice(
        choices: UrbanRegulationStructure::CHOICES,
        message: 'Seleccione la estructura sobre la cual se aplica esta regulación.'
    )]
    private UrbanRegulationStructure $enumStructure;

    public function __construct()
    {
        $this->projectUrbanRegulations = new ArrayCollection();
    }

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
        return $this->measurementUnit;
    }

    public function setMeasurementUnit(string $measurementUnit): static
    {
        $this->measurementUnit = $measurementUnit;

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

    /**
     * @return Collection<int, ProjectUrbanRegulation>
     */
    public function getProjectUrbanRegulations(): Collection
    {
        return $this->projectUrbanRegulations;
    }

    public function addProjectUrbanRegulation(ProjectUrbanRegulation $projectUrbanRegulation): static
    {
        if (!$this->projectUrbanRegulations->contains($projectUrbanRegulation)) {
            $this->projectUrbanRegulations->add($projectUrbanRegulation);
        }

        return $this;
    }

    public function removeProjectUrbanRegulation(ProjectUrbanRegulation $projectUrbanRegulation): static
    {
        $this->projectUrbanRegulations->removeElement($projectUrbanRegulation);

        return $this;
    }

    public function getStructure(): UrbanRegulationStructure
    {
        return $this->enumStructure;
    }

    public function setStructure(UrbanRegulationStructure $enumStructure): static
    {
        $this->structure = '';
        $this->enumStructure = $enumStructure;

        return $this;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function onSave(): void
    {
        $this->structure = $this->getStructure()->value;
    }

    /**
     * @throws \Exception
     */
    #[ORM\PostLoad]
    public function onLoad(): void
    {
        $this->setStructure(UrbanRegulationStructure::from($this->structure));
    }
}
