<?php

namespace App\Entity;

use App\Repository\EnterpriseClientRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;

#[ORM\Entity(repositoryClass: EnterpriseClientRepository::class)]
#[DoctrineAssert\UniqueEntity(fields: ['email'], message: 'Ya existe un cliente con este correo.')]
class EnterpriseClient extends Client
{
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

    public function getRepresentativeName(): ?string
    {
        if(!is_null($this->getRepresentative())){
            return $this->getRepresentative()->getName();
        }

        return "";
    }

    public function getRepresentativeIdentificationNumber(): ?string
    {
        if(!is_null($this->getRepresentative())){
            return $this->getRepresentative()->getIdentificationNumber();
        }

        return "";
    }

    public function getRepresentativePassport(): ?string
    {
        if(!is_null($this->getRepresentative())){
            return $this->getRepresentative()->getPassport();
        }

        return "";
    }

}
