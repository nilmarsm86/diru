<?php

namespace App\Entity;

use App\Repository\RoleRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\NameToStringTrait;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;

#[ORM\Entity(repositoryClass: RoleRepository::class)]
#[ORM\UniqueConstraint(name: 'role_name', columns: ['name'])]
#[DoctrineAssert\UniqueEntity(fields: ['name'], message: 'Ya existe un rol con este nombre.')]
class Role
{
    use NameToStringTrait;

    const  ROLE_CLIENT = 'ROLE_CLIENT';
    const  ROLE_DIRECTOR = 'ROLE_DIRECTOR';
    const  ROLE_INVESTOR = 'ROLE_INVESTOR';
    const  ROLE_DRAFTSMAN = 'ROLE_DRAFTSMAN';
    const  ROLE_ADMIN = 'ROLE_ADMIN';
    const  ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';
    const  IS_AUTHENTICATED = 'IS_AUTHENTICATED';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $importance = null;

    /**
     * @param string $name
     * @param int $importance
     */
    public function __construct(string $name, int $importance)
    {
        $this->name = $name;
        $this->importance = $importance;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Return translate rol name
     * @param string|null $role
     * @return string
     */
    public function capitalizeName(string $role = null): string
    {
        return match ($role ?: $this->getName()) {
            static::ROLE_CLIENT => 'Rol cliente',
            static::ROLE_DIRECTOR => 'Rol director',
            static::ROLE_INVESTOR => 'Rol inversionista',
            static::ROLE_DRAFTSMAN => 'Rol proyectista',
            static::ROLE_ADMIN => 'Rol admin',
            static::ROLE_SUPER_ADMIN => 'Rol super admin',
            default => throw new \InvalidArgumentException('Valor no soportado'),
        };
    }

    public function isSuperAdmin(): bool
    {
        return $this->getName() === static::ROLE_SUPER_ADMIN;
    }

    public function saveName(string $role = null): string
    {
        return match ($role ?: $this->getName()) {
            static::ROLE_CLIENT => 'client',
            static::ROLE_DIRECTOR => 'director',
            static::ROLE_INVESTOR => 'investor',
            static::ROLE_DRAFTSMAN => 'draftsman',
            static::ROLE_ADMIN => 'admin',
            static::ROLE_SUPER_ADMIN => 'super_admin',
            default => throw new \InvalidArgumentException('Valor no soportado'),
        };
    }

    /**
     * Can change this role for all users
     * @return bool
     */
    public function blockChange(): bool
    {
        return $this->getName() === static::ROLE_CLIENT;
    }

    public function getImportance(): ?int
    {
        return $this->importance;
    }

    public function setImportance(int $importance): static
    {
        $this->importance = $importance;

        return $this;
    }
}
