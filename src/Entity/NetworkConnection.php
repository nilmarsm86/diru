<?php

namespace App\Entity;

use App\Entity\Traits\NameToStringTrait;
use App\Repository\NetworkConnectionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: NetworkConnectionRepository::class)]
class NetworkConnection
{
    use NameToStringTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var Collection<int, LandNetworkConnection>
     */
    #[ORM\OneToMany(targetEntity: LandNetworkConnection::class, mappedBy: 'networkConnection')]
    #[Assert\Valid]
    private Collection $landNetworkConnections;

    public function __construct()
    {
        $this->landNetworkConnections = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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
            $landNetworkConnection->setNetworkConnection($this);
        }

        return $this;
    }

    public function removeLandNetworkConnection(LandNetworkConnection $landNetworkConnection): static
    {
        if ($this->landNetworkConnections->removeElement($landNetworkConnection)) {
            // set the owning side to null (unless already changed)
            if ($landNetworkConnection->getNetworkConnection() === $this) {
                $landNetworkConnection->setNetworkConnection(null);
            }
        }

        return $this;
    }
}
