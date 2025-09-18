<?php

namespace App\Entity;

use App\Entity\Traits\NameToStringTrait;
use App\Repository\UrbanRegulationTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UrbanRegulationTypeRepository::class)]
class UrbanRegulationType
{
    use NameToStringTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var Collection<int, UrbanRegulation>
     */
    #[ORM\OneToMany(targetEntity: UrbanRegulation::class, mappedBy: 'type')]
    private Collection $urbanRegulations;

    public function __construct()
    {
        $this->urbanRegulations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, UrbanRegulation>
     */
    public function getUrbanRegulations(): Collection
    {
        return $this->urbanRegulations;
    }

    public function addUrbanRegulation(UrbanRegulation $urbanRegulation): static
    {
        if (!$this->urbanRegulations->contains($urbanRegulation)) {
            $this->urbanRegulations->add($urbanRegulation);
            $urbanRegulation->setType($this);
        }

        return $this;
    }

    public function removeUrbanRegulation(UrbanRegulation $urbanRegulation): static
    {
        if ($this->urbanRegulations->removeElement($urbanRegulation)) {
            // set the owning side to null (unless already changed)
            if ($urbanRegulation->getType() === $this) {
                $urbanRegulation->setType(null);
            }
        }

        return $this;
    }
}
