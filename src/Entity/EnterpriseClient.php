<?php

namespace App\Entity;

use App\Repository\EnterpriseClientRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EnterpriseClientRepository::class)]
class EnterpriseClient extends Client
{
    #[ORM\ManyToOne(inversedBy: 'enterpriseClients')]
    #[ORM\JoinColumn(nullable: false)]
    private ?CorporateEntity $corporateEntity = null;

    #[ORM\Column]
    private ?bool $activeContract = false;

    public function getCorporateEntity(): ?CorporateEntity
    {
        return $this->corporateEntity;
    }

    public function setCorporateEntity(?CorporateEntity $corporateEntity): static
    {
        $this->corporateEntity = $corporateEntity;

        return $this;
    }

    public function isActiveContract(): ?bool
    {
        return $this->activeContract;
    }

    public function setActiveContract(bool $activeContract): static
    {
        $this->activeContract = $activeContract;

        return $this;
    }
}
