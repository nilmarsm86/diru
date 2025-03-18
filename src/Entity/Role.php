<?php

namespace App\Entity;

use App\Repository\RoleRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RoleRepository::class)]
class Role
{
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
            'ROLE_CLIENT' => 'Rol cliente',
            'ROLE_DIRECTOR' => 'Rol director',
            'ROLE_PLANNER' => 'Rol planificador',
            'ROLE_ADMIN' => 'Rol admin',
            'ROLE_SUPER_ADMIN' => 'Rol super admin',
        };
    }

    public function isSuperAdmin(): bool
    {
        return $this->getName() === 'ROLE_SUPER_ADMIN';
    }

}
