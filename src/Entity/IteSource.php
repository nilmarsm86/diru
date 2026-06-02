<?php

namespace App\Entity;

use App\Entity\Traits\NameToStringTrait;
use App\Repository\IteSourceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;

#[ORM\Entity(repositoryClass: IteSourceRepository::class)]
#[ORM\UniqueConstraint(name: 'source_name', columns: ['name'])]
#[DoctrineAssert\UniqueEntity('name', message: 'La fuente ya existe.')]
class IteSource
{
    use NameToStringTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var Collection<int, Ite>
     */
    #[ORM\OneToMany(targetEntity: Ite::class, mappedBy: 'source', orphanRemoval: true)]
    private Collection $ites;

    public function __construct()
    {
        $this->ites = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, Ite>
     */
    public function getItes(): Collection
    {
        return $this->ites;
    }

    public function addIte(Ite $ite): static
    {
        if (!$this->ites->contains($ite)) {
            $this->ites->add($ite);
            $ite->setSource($this);
        }

        return $this;
    }

    public function removeIte(Ite $ite): static
    {
        if ($this->ites->removeElement($ite)) {
            // set the owning side to null (unless already changed)
            if ($ite->getSource() === $this) {
                $ite->setSource(null);
            }
        }

        return $this;
    }
}
