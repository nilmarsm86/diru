<?php

namespace App\Entity;

use App\Entity\Enums\LocalTechnicalStatus;
use App\Entity\Enums\LocalType;
use App\Entity\Enums\ProjectType;
use App\Entity\Traits\NameToStringTrait;
use App\Repository\LocalRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: LocalRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Local
{
    use NameToStringTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $number = null;

    #[ORM\Column]
    private ?int $area = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[Assert\Choice(
        choices: LocalType::CHOICES,
        message: 'Seleccione un tipo de local.'
    )]
    private LocalType $enumType;

    #[ORM\Column]
    private ?int $height = null;

    #[ORM\Column(length: 255)]
    private ?string $technicalStatus = null;

    #[Assert\Choice(
        choices: LocalTechnicalStatus::CHOICES,
        message: 'Seleccione el estado técnico del local.'
    )]
    private LocalType $enumTechnicalStatus;

    #[ORM\Column(enumType: LocalType::class)]
    private ?LocalType $type2 = null;

    #[ORM\ManyToOne(inversedBy: 'locals')]
    #[ORM\JoinColumn(nullable: false)]
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

    public function getType2(): ?LocalType
    {
        return $this->type2;
    }

    public function setType2(LocalType $type2): static
    {
        $this->type2 = $type2;

        return $this;
    }

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
        $this->state = $this->getState()->value;
        if(is_null($this->getId())){
            if (is_null($this->getContract()) || is_null($this->getContract()->getCode())) {
                $this->setContract(null);
            }
        }
    }
}
