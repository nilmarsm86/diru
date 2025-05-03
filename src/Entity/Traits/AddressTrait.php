<?php

namespace App\Entity\Traits;

use App\Entity\Enums\State as StateEnum;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

trait AddressTrait
 {
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
 }