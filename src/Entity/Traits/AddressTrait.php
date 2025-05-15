<?php

namespace App\Entity\Traits;

use App\Entity\Municipality;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

trait AddressTrait
 {
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\Valid]
    private ?Municipality $municipality = null;

    /**
     * @return string
     */
    public function getMunicipalityName(): string
    {
        return $this->municipality->getName();
    }

    /**
     * @return string
     */
    public function getProvinceName(): string
    {
        return $this->municipality->getProvince()->getName();
    }

    public function getMunicipality(): ?Municipality
    {
        return $this->municipality;
    }

    public function setMunicipality(?Municipality $municipality): static
    {
        $this->municipality = $municipality;

        return $this;
    }
 }