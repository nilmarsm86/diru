<?php

namespace App\Entity;

use App\Entity\Traits\StateTrait;
use App\Repository\BuildingRevisionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BuildingRevisionRepository::class)]
#[ORM\HasLifecycleCallbacks]
class BuildingRevision
{
    use StateTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $modifiedAt = null;

    #[ORM\Column(type: Types::TEXT)]
    //    #[Assert\NotBlank(message: 'La revisión no puede estar vacía.')]
    private ?string $comment = null;

    #[ORM\ManyToOne(inversedBy: 'buildingRevisions')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\Valid]
    private ?Building $building = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable('now');
        $this->activate();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getModifiedAt(): ?\DateTimeImmutable
    {
        return $this->modifiedAt;
    }

    public function setModifiedAt(\DateTimeImmutable $modifiedAt): static
    {
        $this->modifiedAt = $modifiedAt;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(string $comment): static
    {
        $this->comment = $comment;

        return $this;
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

    #[ORM\PreUpdate]
    public function onModified(): void
    {
        $this->modifiedAt = new \DateTimeImmutable('now');
    }
}
