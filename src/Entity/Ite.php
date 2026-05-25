<?php

namespace App\Entity;

use App\Entity\Enums\IteQuality;
use App\Entity\Enums\IteType;
use App\Repository\IteRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: IteRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Ite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $type;

    #[Assert\Choice(
        choices: IteType::CHOICES,
        message: 'Seleccione el tipo de ITE.'
    )]
    private IteType $enumType;

    #[ORM\Column(length: 255)]
    private string $quality;

    #[Assert\Choice(
        choices: IteQuality::CHOICES,
        message: 'Seleccione la calidad.'
    )]
    private IteQuality $enumQuality;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?MeasurementUnit $measurementUnit = null;

    #[ORM\Column]
    private ?float $min = null;

    #[ORM\Column]
    private ?float $max = null;

    #[ORM\Column]
    private ?int $yearReference = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $comment = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $sourceAccess = null;

    #[ORM\ManyToOne(cascade: ['persist'], inversedBy: 'ites')]
    #[ORM\JoinColumn(nullable: false)]
    private ?IteSource $source = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[ORM\OrderBy(['name' => 'DESC'])]
    private ?City $city = null;

    #[ORM\ManyToOne(cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?IteProjectType $projectType = null;

    public function __construct()
    {
        $this->setQuality(IteQuality::Medium);
        $this->setType(IteType::National);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): IteType
    {
        return $this->enumType;
    }

    public function setType(IteType $enumType): static
    {
        $this->type = '';
        $this->enumType = $enumType;

        return $this;
    }

    public function getQuality(): IteQuality
    {
        return $this->enumQuality;
    }

    public function setQuality(IteQuality $enumQuality): static
    {
        $this->quality = '';
        $this->enumQuality = $enumQuality;

        return $this;
    }

    public function getMeasurementUnit(): ?MeasurementUnit
    {
        return $this->measurementUnit;
    }

    public function setMeasurementUnit(?MeasurementUnit $measurementUnit): static
    {
        $this->measurementUnit = $measurementUnit;

        return $this;
    }

    public function getMin(): ?float
    {
        return $this->min;
    }

    public function setMin(float $min): static
    {
        $this->min = $min;

        return $this;
    }

    public function getMax(): ?float
    {
        return $this->max;
    }

    public function setMax(float $max): static
    {
        $this->max = $max;

        return $this;
    }

    public function getYearReference(): ?int
    {
        return $this->yearReference;
    }

    public function setYearReference(int $yearReference): static
    {
        $this->yearReference = $yearReference;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): static
    {
        $this->comment = $comment;

        return $this;
    }

    public function getSourceAccess(): ?string
    {
        return $this->sourceAccess;
    }

    public function setSourceAccess(?string $sourceAccess): static
    {
        $this->sourceAccess = $sourceAccess;

        return $this;
    }

    public function getSource(): ?IteSource
    {
        return $this->source;
    }

    public function setSource(?IteSource $source): static
    {
        $this->source = $source;

        return $this;
    }

    public function getCity(): ?City
    {
        return $this->city;
    }

    public function setCity(?City $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getProjectType(): ?IteProjectType
    {
        return $this->projectType;
    }

    public function setProjectType(?IteProjectType $projectType): static
    {
        $this->projectType = $projectType;

        return $this;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function onSave(): void
    {
        $this->quality = $this->getQuality()->value;
        $this->type = $this->getType()->value;
    }

    /**
     * @throws \Exception
     */
    #[ORM\PostLoad]
    public function onLoad(): void
    {
        $this->setQuality(IteQuality::from($this->quality));
        $this->setType(IteType::from($this->type));
    }
}
