<?php

namespace App\Entity;

use App\Entity\Traits\NameToStringTrait;
use App\Repository\SubsystemSubTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SubsystemSubTypeRepository::class)]
//#[ORM\UniqueConstraint(name: 'municipality_name', columns: ['name'])]
//#[DoctrineAssert\UniqueEntity(fields: ['name', 'subsystemType'], message: 'Ya existe en tipo este subtipo.', errorPath: 'name')]
#[ORM\HasLifecycleCallbacks]
class SubsystemSubType
{
    use NameToStringTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToMany(targetEntity: SubsystemType::class, inversedBy: 'subsystemSubTypes')]
//    #[ORM\JoinColumn(nullable: false)]
//    #[Assert\Valid]
//    #[Ignore]
//    #[Assert\NotBlank(message: 'Seleccione o cree la tipo a la cual pertenece el subtipo.')]
    private ?Collection $subsystemTypes = null;

    public function __construct()
    {
        $this->subsystemTypes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

//    public function getSubsystemType(): ?SubsystemType
//    {
//        return $this->subsystemType;
//    }
//
//    public function setSubsystemType(?SubsystemType $subsystemType): static
//    {
//        $this->subsystemType = $subsystemType;
//
//        return $this;
//    }

    /**
     * @return Collection<int, SubsystemType>
     */
    public function getSubsystemTypes(): Collection
    {
        return $this->subsystemTypes;
    }

    public function addSubsystemType(SubsystemType $subsystemType): static
    {
        if (!$this->subsystemTypes->contains($subsystemType)) {
            $this->subsystemTypes->add($subsystemType);
            //$subsystemType->addSubsystemSubType($this);
        }

        return $this;
    }

    public function removeSubsystemType(SubsystemType $subsystemType): static
    {
//        if ($this->subsystemTypes->removeElement($subsystemType)) {
//            // set the owning side to null (unless already changed)
//            if ($subsystemType->getSubsystemType() === $this) {
//                $subsystemType->setSubsystemType(null);
//            }
//        }
        $this->subsystemTypes->removeElement($subsystemType);

        return $this;
    }



}
