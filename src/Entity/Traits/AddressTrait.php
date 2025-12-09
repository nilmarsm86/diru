<?php

namespace App\Entity\Traits;

use App\Entity\Municipality;
use App\Entity\Province;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

trait AddressTrait
 {
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\Valid]
    private ?Municipality $municipality = null;

    /**
     * @return string|null
     */
    public function getMunicipalityName(): ?string
    {
        return $this->municipality?->getName();
    }

    /**
     * @return string|null
     */
    public function getProvinceName(): ?string
    {
        $province = $this->municipality?->getProvince();
        return $province?->getName();
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
