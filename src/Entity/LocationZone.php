<?php

namespace App\Entity;

use App\Entity\Traits\NameToStringTrait;
use App\Repository\LocationZoneRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: LocationZoneRepository::class)]
#[DoctrineAssert\UniqueEntity(fields: ['name'], message: 'Ya existe una Zona de ubicación con este nombre.')]
class LocationZone
{
    use NameToStringTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var Collection<int, Investment>
     */
    #[ORM\OneToMany(targetEntity: Investment::class, mappedBy: 'locationZone')]
    #[Assert\Valid]
    private Collection $investments;

    public function __construct()
    {
        $this->investments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, Investment>
     */
    public function getInvestments(): Collection
    {
        return $this->investments;
    }

    public function addInvestment(Investment $investment): static
    {
        if (!$this->investments->contains($investment)) {
            $this->investments->add($investment);
            $investment->setLocationZone($this);
        }

        return $this;
    }

    public function removeInvestment(Investment $investment): static
    {
        if ($this->investments->removeElement($investment)) {
            // set the owning side to null (unless already changed)
            if ($investment->getLocationZone() === $this) {
                $investment->setLocationZone(null);
            }
        }

        return $this;
    }
}
