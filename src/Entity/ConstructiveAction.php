<?php

namespace App\Entity;

use App\Entity\Enums\ConstructiveActionType;
use App\Entity\Traits\NameToStringTrait;
use App\Repository\ConstructiveActionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ConstructiveActionRepository::class)]
#[ORM\HasLifecycleCallbacks]
class ConstructiveAction
{

    use NameToStringTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[Assert\Choice(
        choices: [ConstructiveActionType::NoModifier, ConstructiveActionType::Modifier],
        message: 'Seleccione un tipo de proyecto.'
    )]
    private ConstructiveActionType $enumType;

    /**
     * @var Collection<int, Local>
     */
    #[ORM\OneToMany(targetEntity: Local::class, mappedBy: 'constructiveAction')]
    private Collection $locals;

    public function __construct()
    {
        $this->locals = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ConstructiveActionType
    {
        return $this->enumType;
    }

    public function setType(ConstructiveActionType $enumType): static
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
        $this->setType(ConstructiveActionType::from($this->type));
    }

    /**
     * @return Collection<int, Local>
     */
    public function getLocals(): Collection
    {
        return $this->locals;
    }

    public function addLocal(Local $local): static
    {
        if (!$this->locals->contains($local)) {
            $this->locals->add($local);
            $local->setConstructiveAction($this);
        }

        return $this;
    }

    public function removeLocal(Local $local): static
    {
        if ($this->locals->removeElement($local)) {
            // set the owning side to null (unless already changed)
            if ($local->getConstructiveAction() === $this) {
                $local->setConstructiveAction(null);
            }
        }

        return $this;
    }
}
