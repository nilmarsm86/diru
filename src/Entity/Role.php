<?php

namespace App\Entity;

use App\Entity\Traits\NameToStringTrait;
use App\Repository\RoleRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;

#[ORM\Entity(repositoryClass: RoleRepository::class)]
#[ORM\UniqueConstraint(name: 'name', columns: ['name'])]
#[DoctrineAssert\UniqueEntity(fields: ['name'], message: 'Ya existe un rol con este nombre.')]
class Role
{
    use NameToStringTrait;

    const string ROLE_CLIENT = 'ROLE_CLIENT';
    const string ROLE_DIRECTOR = 'ROLE_DIRECTOR';
    const string ROLE_PLANNER = 'ROLE_PLANNER';
    const string ROLE_ADMIN = 'ROLE_ADMIN';
    const string ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
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
            static::ROLE_PLANNER => 'Rol planificador',
            static::ROLE_ADMIN => 'Rol admin',
            static::ROLE_SUPER_ADMIN => 'Rol super admin',
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
            static::ROLE_PLANNER => 'planner',
            static::ROLE_ADMIN => 'admin',
            static::ROLE_SUPER_ADMIN => 'super_admin',
        };
    }

}
