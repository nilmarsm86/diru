<?php

namespace App\Entity;

use App\Repository\LandNetworkConnectionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LandNetworkConnectionRepository::class)]
class LandNetworkConnection
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'landNetworkConnections')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Land $land = null;

    #[ORM\ManyToOne(inversedBy: 'landNetworkConnections')]
    #[ORM\JoinColumn(nullable: false)]
    private ?NetworkConnection $networkConnection = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $explanation = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLand(): ?Land
    {
        return $this->land;
    }

    public function setLand(?Land $land): static
    {
        $this->land = $land;

        return $this;
    }

    public function getNetworkConnection(): ?NetworkConnection
    {
        return $this->networkConnection;
    }

    public function setNetworkConnection(?NetworkConnection $networkConnection): static
    {
        $this->networkConnection = $networkConnection;

        return $this;
    }

    public function getExplanation(): ?string
    {
        return $this->explanation;
    }

    public function setExplanation(?string $explanation): static
    {
        $this->explanation = $explanation;

        return $this;
    }

}
