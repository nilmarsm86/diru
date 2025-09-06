<?php

namespace App\Entity;

use App\Entity\Enums\NetworkConnectionType;
use App\Entity\Traits\TechnicalStatusTrait;
use App\Repository\LandNetworkConnectionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: LandNetworkConnectionRepository::class)]
#[ORM\HasLifecycleCallbacks]
class LandNetworkConnection
{
    use TechnicalStatusTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'landNetworkConnections')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\Valid]
    #[Assert\NotBlank(message: 'Establezca la obra')]
    private ?Building $building = null;

    #[ORM\ManyToOne(inversedBy: 'landNetworkConnections')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\Valid]
    #[Assert\NotBlank(message: 'Establezca el tipo de red')]
    private ?NetworkConnection $networkConnection = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $explanation = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[Assert\Choice(
        choices: NetworkConnectionType::CHOICES,
        message: 'Seleccione un tipo de conexión de red.'
    )]
    public NetworkConnectionType $enumType;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Positive(message: 'La longitud debe ser positiva.')]
    private ?string $longitude = null;

    #[ORM\OneToOne(inversedBy: 'landNetworkConnection', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: true)]
    #[Assert\Valid]
    private ?LandNetworkConnectionConstructiveAction $landNetworkConnectionConstructiveAction = null;

    public function __construct()
    {
        $this->landNetworkConnectionConstructiveAction = null;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBuilding(): ?Building
    {
        return $this->building;
    }

    public function setBuilding(?Building $building): static
    {
        $this->building = $building;

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

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function onSave(): void
    {
        $this->type = $this->getType()->value;
    }

    #[ORM\PostLoad]
    public function onLoad(): void
    {
        $this->setType(NetworkConnectionType::from($this->type));
    }
    public function getType(): NetworkConnectionType
    {
        return $this->enumType;
    }

    public function setType(NetworkConnectionType $enumType): static
    {
        $this->type = '';
        $this->enumType = $enumType;
        return $this;
    }

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    public function setLongitude(string $longitude): static
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getLandNetworkConnectionConstructiveAction(): ?LandNetworkConnectionConstructiveAction
    {
        return $this->landNetworkConnectionConstructiveAction;
    }

    public function hasLandNetworkConnectionConstructiveAction(): bool
    {
        return !is_null($this->getLandNetworkConnectionConstructiveAction());
    }

    public function setLandNetworkConnectionConstructiveAction(?LandNetworkConnectionConstructiveAction $landNetworkConnectionConstructiveAction): static
    {
        $this->landNetworkConnectionConstructiveAction = $landNetworkConnectionConstructiveAction;

        return $this;
    }

}
