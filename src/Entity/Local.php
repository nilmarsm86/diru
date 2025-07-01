<?php

namespace App\Entity;

use App\Entity\Enums\LocalTechnicalStatus;
use App\Entity\Enums\LocalType;
use App\Entity\Traits\NameToStringTrait;
use App\Repository\LocalRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;

#[ORM\Entity(repositoryClass: LocalRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[DoctrineAssert\UniqueEntity(fields: ['name', 'floor'], message: 'Ya existe en la planta un local con este nombre.', errorPath: 'name')]
class Local
{
    use NameToStringTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'El número del local esta vacío.')]
    private ?int $number = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'El área esta vacía.')]
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
    #[Assert\NotBlank(message: 'La altura esta vacía.')]
    private ?int $height = null;

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
    #[Assert\Valid]
//    #[Assert\NotBlank(message: 'Establezca la planta.')]
    private ?Floor $floor = null;

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

    public function getHeight(): ?int
    {
        return $this->height;
    }

    public function setHeight(int $height): static
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

//    public function getType2(): ?LocalType
//    {
//        return $this->type2;
//    }
//
//    public function setType2(LocalType $type2): static
//    {
//        $this->type2 = $type2;
//
//        return $this;
//    }

    public function getFloor(): ?Floor
    {
        return $this->floor;
    }

    public function setFloor(?Floor $floor): static
    {
        $this->floor = $floor;

        return $this;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function onSave(): void
    {
        $this->type = $this->getType()->value;
        $this->technicalStatus = $this->getTechnicalStatus()->value;
    }

    #[ORM\PostLoad]
    public function onLoad(): void
    {
        $this->setType(LocalType::from($this->type));
        $this->setTechnicalStatus(LocalTechnicalStatus::from($this->technicalStatus));
    }

    public function getVolume()
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
        $localWall->setNumber(0);
        $localWall->setTechnicalStatus(LocalTechnicalStatus::Undefined);

        return $localWall;
    }
}
