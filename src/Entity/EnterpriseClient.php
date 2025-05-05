<?php

namespace App\Entity;

use App\Entity\Traits\AddressTrait;
use App\Repository\EnterpriseClientRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EnterpriseClientRepository::class)]
class EnterpriseClient extends Client
{
    use AddressTrait;

    #[ORM\ManyToOne(inversedBy: 'enterpriseClients')]
    #[ORM\JoinColumn(nullable: false)]
    private ?CorporateEntity $corporateEntity = null;

    public function getCorporateEntity(): ?CorporateEntity
    {
        return $this->corporateEntity;
    }

    public function setCorporateEntity(?CorporateEntity $corporateEntity): static
    {
        $this->corporateEntity = $corporateEntity;

        return $this;
    }

}
