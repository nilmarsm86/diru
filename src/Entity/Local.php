<?php

namespace App\Entity;

use App\Entity\Enums\LocalTechnicalStatus;
use App\Entity\Enums\LocalType;
use App\Entity\Interfaces\MeasurementDataInterface;
use App\Entity\Traits\NameToStringTrait;
use App\Entity\Traits\OriginalTrait;
use App\Repository\LocalRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;

#[ORM\Entity(repositoryClass: LocalRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[DoctrineAssert\UniqueEntity(fields: ['name', 'subSystem'], message: 'Ya existe en el sub sistema un local con este nombre.', errorPath: 'name')]
#[DoctrineAssert\UniqueEntity(fields: ['number', 'subSystem'], message: 'Ya existe en el sub sistema un local con este número.', errorPath: 'number')]
class Local
{
    use NameToStringTrait;
    use OriginalTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'El número del local está vacío.')]
    private ?int $number = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'El área está vacía.')]
//    #[Assert\Expression(
//        "this.getFloor().getBuilding().getLandArea() < value",
//        message: 'No debe ser mayor que el area de la obra.',
//    )]
    private ?int $area = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[Assert\Choice(
        choices: LocalType::CHOICES,
        message: 'Seleccione un tipo de local.'
    )]
    private LocalType $enumType;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'La altura está vacía.')]
    private ?float $height = null;

    #[ORM\Column(length: 255)]
    private ?string $technicalStatus = null;

    #[Assert\Choice(
        choices: LocalTechnicalStatus::CHOICES,
        message: 'Seleccione el estado técnico del local.'
    )]
    private LocalTechnicalStatus $enumTechnicalStatus;

//    #[ORM\Column(enumType: LocalType::class)]
//    private ?LocalType $type2 = null;

    #[ORM\ManyToOne(inversedBy: 'locals')]
    #[ORM\JoinColumn(nullable: false)]
//    #[Assert\Valid]
//    #[Assert\NotBlank(message: 'Establezca el subsistema.')]
    private ?SubSystem $subSystem = null;

    #[ORM\Column]
    private ?bool $impactHigherLevels = null;

    public function __construct()
    {
        $this->impactHigherLevels = false;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumber(): ?int
    {
        return $this->number;
    }

    public function setNumber(int $number): static
    {
        $this->number = $number;

        return $this;
    }

    public function getArea(): ?int
    {
        return $this->area;
    }

    public function setArea(int $area): static
    {
        $this->area = $area;

        return $this;
    }

    public function getType(): LocalType
    {
        return $this->enumType;
    }

    public function setType(LocalType $enumType): static
    {
        $this->type = '';
        $this->enumType = $enumType;
        return $this;
    }

    public function getHeight(): ?float
    {
        return $this->height;
    }

    public function setHeight(float $height): static
    {
        $this->height = $height;

        return $this;
    }

    public function getTechnicalStatus(): LocalTechnicalStatus
    {
        return $this->enumTechnicalStatus;
    }

    public function setTechnicalStatus(LocalTechnicalStatus $enumTechnicalStatus): static
    {
        $this->technicalStatus = '';
        $this->enumTechnicalStatus = $enumTechnicalStatus;

        return $this;
    }

    public function getSubSystem(): ?SubSystem
    {
        return $this->subSystem;
    }

    public function setSubSystem(?SubSystem $subSystem): static
    {
        $this->subSystem = $subSystem;

        return $this;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function onSave(): void
    {
        $this->type = $this->getType()->value;
        $this->technicalStatus = $this->getTechnicalStatus()->value;

        if($this->getType() == LocalType::WallArea){
            $this->setName('Área de muro');
            if(is_null($this->getId())){
                $this->setNumber($this->getSubSystem()->getMaxLocalNumber() + 1);
            }
        }

        if($this->getType() == LocalType::EmptyArea){
            $this->setHeight(0);
        }
    }

    #[ORM\PostLoad]
    public function onLoad(): void
    {
        $this->setType(LocalType::from($this->type));
        $this->setTechnicalStatus(LocalTechnicalStatus::from($this->technicalStatus));
    }

    public function getVolume(): float|int
    {
        return $this->getArea() * $this->getHeight();
    }

    public static function createAutomaticWall(int $area): self
    {
        $localWall = new Local();
        $localWall->setName('Área de muro');
        $localWall->setType(LocalType::WallArea);
        $localWall->setArea($area);
        $localWall->setHeight(0);
//        $localWall->setNumber(0);
        $localWall->setTechnicalStatus(LocalTechnicalStatus::Undefined);

        return $localWall;
    }

    public function isClassified(): bool
    {
        return $this->getTechnicalStatus() !== LocalTechnicalStatus::Undefined;
    }

    public function reply(EntityManagerInterface $entityManager): Floor|static
    {
        $replica = clone $this;
        $replica->setOriginal($this);

        $entityManager->persist($replica);

        return $replica;
    }

    public function isImpactHigherLevels(): ?bool
    {
        return $this->impactHigherLevels;
    }

    public function setImpactHigherLevels(bool $impactHigherLevels): static
    {
        $this->impactHigherLevels = $impactHigherLevels;

        return $this;
    }
}
