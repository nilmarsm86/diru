<?php

namespace App\Entity;

use App\Entity\Traits\NameToStringTrait;
use App\Repository\SubsystemSubTypeRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SubsystemSubTypeRepository::class)]
#[DoctrineAssert\UniqueEntity(fields: ['name', 'subsystemType'], message: 'Ya existe en la clasificación esta subclasificación.', errorPath: 'name')]
#[ORM\HasLifecycleCallbacks]
class SubsystemSubType
{
    use NameToStringTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'subTypes')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\Valid]
    #[Ignore]
    #[Assert\NotBlank(message: 'Seleccione o cree el tipo de subsistema a la cual pertenece el sub tipo de subsistema.')]
    private ?SubsystemType $subsystemType = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSubSystemType(): ?SubsystemType
    {
        return $this->subsystemType;
    }

    public function setSubsystemType(?SubsystemType $subsystemType): static
    {
        $this->subsystemType = $subsystemType;

        return $this;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function onSave(): void
    {
        $this->name = ucwords($this->getName());
    }

}
