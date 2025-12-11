<?php

namespace App\Entity;

use App\Entity\Traits\NameToStringTrait;
use App\Repository\ProvinceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProvinceRepository::class)]
#[ORM\UniqueConstraint(name: 'province_name', columns: ['name'])]
#[DoctrineAssert\UniqueEntity('name', message: 'La provincia ya existe.')]
#[ORM\HasLifecycleCallbacks]
class Province
{
    use NameToStringTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /** @var Collection<int, Municipality> */
    #[ORM\OneToMany(targetEntity: Municipality::class, mappedBy: 'province', cascade: ['persist'], orphanRemoval: true)]
    #[Assert\Count(
        min: 1,
        minMessage: 'Debe establecer al menos 1 municipio para esta provincia.',
    )]
    #[Assert\Valid]
    #[ORM\OrderBy(['name' => 'ASC'])]
    private Collection $municipalities;

    public function __construct()
    {
        $this->municipalities = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, Municipality>
     */
    public function getMunicipalities(): Collection
    {
        return $this->municipalities;
    }

    public function addMunicipality(Municipality $municipality): static
    {
        if (!$this->municipalities->contains($municipality)) {
            $this->municipalities->add($municipality);
            $municipality->setProvince($this);
        }

        return $this;
    }

    public function removeMunicipality(Municipality $municipality): static
    {
        if ($this->municipalities->removeElement($municipality)) {
            // set the owning side to null (unless already changed)
            if ($municipality->getProvince() === $this) {
                $municipality->setProvince(null);
            }
        }

        return $this;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function onSave(): void
    {
        $this->name = ucwords($this->getName());
    }
}
