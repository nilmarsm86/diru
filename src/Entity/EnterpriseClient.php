<?php

namespace App\Entity;

use App\Entity\Traits\AddressTrait;
use App\Repository\EnterpriseClientRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EnterpriseClientRepository::class)]
class EnterpriseClient extends Client
{
//    use AddressTrait;

    #[ORM\ManyToOne(inversedBy: 'enterpriseClients')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\Valid]
    #[Assert\NotBlank(message: 'Seleccione o cree la entidad a la cual pertenece el cliente.')]
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
