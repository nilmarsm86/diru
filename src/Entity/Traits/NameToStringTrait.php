<?php

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

trait NameToStringTrait
 {
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'El nombre no debe estar vacio.')]
    #[Assert\NotNull(message: 'El nombre no debe ser nulo.')]
    #[Assert\NoSuspiciousCharacters]
    private string $name;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getName();
    }
 }