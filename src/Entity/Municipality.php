<?php

namespace App\Entity;

use App\Entity\Traits\NameToStringTrait;
use App\Repository\MunicipalityRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MunicipalityRepository::class)]
#[DoctrineAssert\UniqueEntity(fields: ['name', 'province'], message: 'Ya existe en la provincia este municipio.', errorPath: 'name')]
#[ORM\HasLifecycleCallbacks]
class Municipality
{
    use NameToStringTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'municipalities')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\Valid]
    #[Ignore]
    #[Assert\NotBlank(message: 'Seleccione o cree la provincia a la cual pertenece el municipio.')]
    private ?Province $province = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProvince(): ?Province
    {
        return $this->province;
    }

    public function setProvince(?Province $province): static
    {
        $this->province = $province;

        return $this;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function onSave(): void
    {
        $this->name = ucwords($this->getName());
    }
}
