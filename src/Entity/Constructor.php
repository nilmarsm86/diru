<?php

namespace App\Entity;

use App\Entity\Traits\AddressTrait;
use App\Entity\Traits\NameToStringTrait;
use App\Repository\ConstructorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ConstructorRepository::class)]
#[ORM\UniqueConstraint(name: 'constructor_name', columns: ['name'])]
#[DoctrineAssert\UniqueEntity('name', message: 'El nombre de la constructora debe ser único.')]
#[DoctrineAssert\UniqueEntity('code', message: 'El código de la constructora debe ser único.')]
class Constructor
{
    use NameToStringTrait;
    use AddressTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'El código está vacío.')]
    private ?string $code = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'El país está vacío.')]
    private ?string $country = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $logo = null;

    //    /**
    //     * @var Collection<int, Building>
    //     */
    //    #[ORM\OneToMany(targetEntity: Building::class, mappedBy: 'constructor')]
    //    private Collection $buildings;

    /**
     * @var Collection<int, ConstructorBuilding>
     */
    #[ORM\OneToMany(targetEntity: ConstructorBuilding::class, mappedBy: 'constructor', cascade: ['persist'])]
    #[Assert\Valid]
    private Collection $constructorBuildings;

    #[ORM\Column(name: 'address', type: Types::TEXT)]
    protected ?string $street = null;

    public function __construct()
    {
        $this->constructorBuildings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): static
    {
        $this->country = $country;

        return $this;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(?string $logo): static
    {
        $this->logo = $logo;

        return $this;
    }

    //    /**
    //     * @return Collection<int, Building>
    //     */
    //    public function getBuildings(): Collection
    //    {
    //        return $this->buildings;
    //    }
    //
    //    public function addBuilding(Building $building): static
    //    {
    //        if (!$this->buildings->contains($building)) {
    //            $this->buildings->add($building);
    //            $building->setConstructor($this);
    //        }
    //
    //        return $this;
    //    }
    //
    //    public function removeBuilding(Building $building): static
    //    {
    //        if ($this->buildings->removeElement($building)) {
    //            // set the owning side to null (unless already changed)
    //            if ($building->getConstructor() === $this) {
    //                $building->setConstructor(null);
    //            }
    //        }
    //
    //        return $this;
    //    }

    /**
     * @return Collection<int, ConstructorBuilding>
     */
    public function getConstructorBuildings(): Collection
    {
        return $this->constructorBuildings;
    }

    public function getConstructorBuildingByBuilding(Building $building): ?ConstructorBuilding
    {
        foreach ($this->getConstructorBuildings() as $constructorBuilding) {
            if ($constructorBuilding->getBuilding()?->getId() === $building->getId()) {
                return $constructorBuilding;
            }
        }

        return null;
    }

    public function addConstructorBuilding(ConstructorBuilding $constructorBuilding): static
    {
        if (!$this->constructorBuildings->contains($constructorBuilding)) {
            $this->constructorBuildings->add($constructorBuilding);
        }

        return $this;
    }

    public function removeConstructorBuilding(ConstructorBuilding $constructorBuilding): static
    {
        $this->constructorBuildings->removeElement($constructorBuilding);

        return $this;
    }

    /**
     * @return Collection<int, Building>
     */
    public function getBuildings(): Collection
    {
        $buildings = new ArrayCollection();
        foreach ($this->getConstructorBuildings() as $constructorBuilding) {
            $buildings->add($constructorBuilding->getBuilding());
        }

        return $buildings;
    }

    public function addBuilding(Building $building): static
    {
        $constructorBuilding = new ConstructorBuilding();
        $constructorBuilding->setBuilding($building);
        $constructorBuilding->setConstructor($this);

        $this->addConstructorBuilding($constructorBuilding);

        return $this;
    }

    public function removeBuilding(Building $building): static
    {
        $constructorBuildings = $building->getConstructorBuildings();
        /** @var ConstructorBuilding $constructorBuilding */
        foreach ($constructorBuildings as $constructorBuilding) {
            if ($constructorBuilding->hasConstructor($this)) {
                $this->removeConstructorBuilding($constructorBuilding);

                return $this;
            }
        }

        return $this;
    }

    public function getBuildingsAmount(): int
    {
        return $this->getBuildings()->count();
    }

    public function hasBuildings(): bool
    {
        return $this->getBuildings()->count() > 0;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(string $street): static
    {
        $this->street = $street;

        return $this;
    }
}
