<?php

namespace App\Entity;

use App\Repository\LandRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: LandRepository::class)]
class Land
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'El área de terreno está vacía.')]
    #[Assert\Positive(message: 'El area de terreno debe ser un número positivo.')]
    private ?int $landArea = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'El área ocupada está vacía.')]
    #[Assert\PositiveOrZero(message: 'El área ocupada debe ser positivo.')]
//    #[Assert\Assert\LessThanOrEqual()]
    private ?int $occupiedArea = null;

    #[ORM\Column(nullable: true)]
    #[Assert\PositiveOrZero(message: 'El perímetro debe ser un número positivo.')]
    private ?int $perimeter = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $photo = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $microlocalization = null;

    /**
     * @var Collection<int, LandNetworkConnection>
     */
        #[ORM\OneToMany(targetEntity: LandNetworkConnection::class, mappedBy: 'land', cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[Assert\Valid]
    private Collection $landNetworkConnections;

    #[ORM\Column]
    private ?int $floor = null;

    public function __construct()
    {
        $this->landNetworkConnections = new ArrayCollection();
        $this->occupiedArea = 0;
        $this->floor = 0;
        $this->landArea = 1;
        $this->perimeter = 0;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLandArea(): ?int
    {
        return $this->landArea;
    }

    public function setLandArea(int $landArea): static
    {
        $this->landArea = $landArea;

        return $this;
    }

    public function getOccupiedArea(): ?int
    {
        return $this->occupiedArea;
    }

    public function setOccupiedArea(int $occupiedArea): static
    {
        $this->occupiedArea = $occupiedArea;

        return $this;
    }

    public function getPerimeter(): ?int
    {
        return $this->perimeter;
    }

    public function setPerimeter(int $perimeter): static
    {
        $this->perimeter = $perimeter;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): static
    {
        $this->photo = $photo;

        return $this;
    }

    public function getMicrolocalization(): ?string
    {
        return $this->microlocalization;
    }

    public function setMicrolocalization(?string $microlocalization): static
    {
        $this->microlocalization = $microlocalization;

        return $this;
    }

    /**
     * @return Collection<int, LandNetworkConnection>
     */
    public function getLandNetworkConnections(): Collection
    {
        return $this->landNetworkConnections;
    }

    public function addLandNetworkConnection(LandNetworkConnection $landNetworkConnection): static
    {
        if (!$this->landNetworkConnections->contains($landNetworkConnection)) {
            $this->landNetworkConnections->add($landNetworkConnection);
            $landNetworkConnection->setLand($this);
        }

        return $this;
    }

    public function removeLandNetworkConnection(LandNetworkConnection $landNetworkConnection): static
    {
        if ($this->landNetworkConnections->removeElement($landNetworkConnection)) {
            // set the owning side to null (unless already changed)
            if ($landNetworkConnection->getLand() === $this) {
                $landNetworkConnection->setLand(null);
            }
        }

        return $this;
    }

    public function getFloor(): ?int
    {
        return $this->floor;
    }

    public function setFloor(?int $floor): static
    {
        $this->floor = $floor;

        return $this;
    }

    public function hasOccupiedArea(): bool
    {
        return $this->occupiedArea > 0;
    }

    public function hasFloors()
    {
        return $this->getFloor() > 0;
    }

}
