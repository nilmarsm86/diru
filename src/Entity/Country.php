<?php

namespace App\Entity;

use App\Entity\Traits\NameToStringTrait;
use App\Repository\CountryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CountryRepository::class)]
#[ORM\UniqueConstraint(name: 'country_name', columns: ['name'])]
#[DoctrineAssert\UniqueEntity('name', message: 'El país ya existe.')]
#[ORM\HasLifecycleCallbacks]
class Country
{
    use NameToStringTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var Collection<int, City>
     */
    #[ORM\OneToMany(targetEntity: City::class, mappedBy: 'country', cascade: ['persist'], orphanRemoval: true)]
    #[Assert\Count(
        min: 1,
        minMessage: 'Debe establecer al menos 1 ciudad para este país.',
    )]
    private Collection $cities;

    public function __construct()
    {
        $this->cities = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, City>
     */
    public function getCities(): Collection
    {
        return $this->cities;
    }

    public function addCity(City $city): static
    {
        if (!$this->cities->contains($city)) {
            $this->cities->add($city);
            $city->setCountry($this);
        }

        return $this;
    }

    public function getCity(string $cityName): ?City
    {
        foreach ($this->cities as $city) {
            if ($city->getName() === $cityName) {
                return $city;
            }
        }

        return null;
    }

    public function removeCity(City $city): static
    {
        if ($this->cities->removeElement($city)) {
            // set the owning side to null (unless already changed)
            if ($city->getCountry() === $this) {
                $city->setCountry(null);
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
