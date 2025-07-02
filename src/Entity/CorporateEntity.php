<?php

namespace App\Entity;

use App\Entity\Traits\AddressTrait;
use App\Entity\Traits\NameToStringTrait;
use App\Repository\CorporateEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Enums\CorporateEntityType;
use Exception;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;

#[ORM\Entity(repositoryClass: CorporateEntityRepository::class)]
#[ORM\UniqueConstraint(name: 'corporate_entity_name', columns: ['name'])]
#[DoctrineAssert\UniqueEntity(fields: ['name'], message: 'Ya existe una entidad con este nombre.')]
#[DoctrineAssert\UniqueEntity(fields: ['code'], message: 'Ya existe una entidad con este código.')]
#[DoctrineAssert\UniqueEntity(fields: ['nit'], message: 'Ya existe una entidad con este NIT.')]
#[ORM\HasLifecycleCallbacks]
class CorporateEntity
{
    use NameToStringTrait;
    use AddressTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'El código de empresa está vacío.')]
//    #[Assert\NotNull(message: 'El codigo de empresa no debe ser nulo.')]
    #[Assert\NoSuspiciousCharacters]
    private ?string $code = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank(message: 'El NIT está vacío.')]
    private ?string $nit = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\Valid]
    #[Assert\NotBlank(message: 'Seleccione o cree el organismo al cual pertenece la entidad.')]
    private ?Organism $organism = null;

    #[ORM\Column(length: 255)]
    private string $type;

    #[Assert\Choice(
        choices: [CorporateEntityType::Client, CorporateEntityType::Constructor, CorporateEntityType::ClientAndConstructor],
        message: 'Seleccione un tipo de entidad.'
    )]
    private CorporateEntityType $enumType;

    /**
     * @var Collection<int, EnterpriseClient>
     */
    #[ORM\OneToMany(targetEntity: EnterpriseClient::class, mappedBy: 'corporateEntity')]
    #[Assert\Valid]
    private Collection $enterpriseClients;

    public function __construct()
    {
        $this->enterpriseClients = new ArrayCollection();
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

    public function getNit(): ?string
    {
        return $this->nit;
    }

    public function setNit(?string $nit): static
    {
        $this->nit = $nit;

        return $this;
    }

    public function getOrganism(): ?Organism
    {
        return $this->organism;
    }

    public function setOrganism(?Organism $organism): static
    {
        $this->organism = $organism;

        return $this;
    }

    public function getType(): CorporateEntityType
    {
        return $this->enumType;
    }

    public function setType(CorporateEntityType $enumType): static
    {
        $this->type = "";
        $this->enumType = $enumType;

        return $this;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function onSave(): void
    {
        $this->type = $this->getType()->value;
    }

    /**
     * @throws Exception
     */
    #[ORM\PostLoad]
    public function onLoad(): void
    {
        $this->setType(CorporateEntityType::from($this->type));
    }

    /**
     * @return Collection<int, EnterpriseClient>
     */
    public function getEnterpriseClients(): Collection
    {
        return $this->enterpriseClients;
    }

    public function addEnterpriseClient(EnterpriseClient $enterpriseClient): static
    {
        if (!$this->enterpriseClients->contains($enterpriseClient)) {
            $this->enterpriseClients->add($enterpriseClient);
            $enterpriseClient->setCorporateEntity($this);
        }

        return $this;
    }

    public function removeEnterpriseClient(EnterpriseClient $enterpriseClient): static
    {
        if ($this->enterpriseClients->removeElement($enterpriseClient)) {
            // set the owning side to null (unless already changed)
            if ($enterpriseClient->getCorporateEntity() === $this) {
                $enterpriseClient->setCorporateEntity(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->getName().' ('.$this->getOrganism()->getName().')';
    }

    public function hasEnterpriseClients(): bool
    {
        return $this->getEnterpriseClients()->count() > 0;
    }
}
