<?php

namespace App\Entity\Traits;

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