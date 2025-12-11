<?php

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

trait PhoneAndEmailTrait
{
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'El teléfono está vacío.')]
    protected ?string $phone = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'El correo está vacío.')]
    protected ?string $email = null;

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }
}
