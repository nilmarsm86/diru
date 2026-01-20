<?php

namespace App\Entity;

use App\Entity\Traits\PhoneAndEmailTrait;
use App\Entity\Traits\StateTrait;
use App\Repository\UserRepository;
use App\Validator\Username;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_USERNAME', fields: ['username'])]
#[DoctrineAssert\UniqueEntity(fields: ['username'], message: 'Ya existe un usuario con este nombre de usuario.')]
#[DoctrineAssert\UniqueEntity(fields: ['email'], message: 'Ya existe un usuario con este correo.')]
#[ORM\HasLifecycleCallbacks]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    use StateTrait;
    use PhoneAndEmailTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\NotBlank(message: 'Establezca el nombre de usuario.')]
    //    #[Assert\NotNull(message: 'El nombre de usuario no puede ser nulo.')]
    #[Assert\NoSuspiciousCharacters]
    #[Username]
    private ?string $username = null;

    /** @var Collection<int, Role> */
    #[ORM\ManyToMany(targetEntity: Role::class)]
    #[Assert\Count(
        min: 1,
        minMessage: 'Debe establecer al menos 1 rol para el usuario.',
    )]
    private Collection $roles;

    /**
     * @var string|null The hashed password
     */
    #[ORM\Column]
    #[Assert\NotBlank(message: 'Establezca la contraseña.')]
    //    #[Assert\NotNull(message: 'La contraseña no puede ser nula.')]
    private ?string $password = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\Valid]
    #[Assert\NotBlank(message: 'Seleccione o cree la persona.')]
    private ?Person $person = null;

    public function __construct(string $name, string $lastname, string $username, string $password, string $identificationNumber, string $phone, string $email, bool $isDraftsman = false)
    {
        $this->person = ($isDraftsman) ? new Draftsman() : new Person();
        $this->person->setName($name);
        $this->person->setLastname($lastname);
        $this->person->setIdentificationNumber($identificationNumber);

        $this->username = $username;
        $this->password = $password;
        $this->phone = $phone;
        $this->email = $email;
        $this->roles = new ArrayCollection();
        $this->deactivate();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPerson(): ?Person
    {
        return $this->person;
    }

    public function setPerson(?Person $person): static
    {
        $this->person = $person;

        return $this;
    }

    public function getUsername(): string
    {
        return (is_null($this->username)) ? '' : $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        if ('' === $this->username || null === $this->username) {
            throw new \LogicException('El identificador del usuario no puede estar vacio');
        }

        return $this->username;
    }

    /**
     * @return array<string>
     *
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = [];
        foreach ($this->roles as $rol) {
            /* @var Role $rol */
            $roles[] = $rol->getName();
        }

        return array_unique($roles);
    }

    /**
     * @throws \Exception
     */
    public function addRole(Role $role): static
    {
        if (!$this->roles->contains($role)) {
            $this->roles->add($role);
        }

        return $this;
    }

    /**
     * @throws \Exception
     */
    public function removeRole(Role $role, bool $secure = true): static
    {
        if (!$this->isActive()) {
            throw new \Exception('No puede eliminar rol de un usuario inactivo.', 1);
        }

        if (Role::ROLE_CLIENT === $role->getName()) {
            throw new \Exception('No puede ser eliminado el rol de cliente.');
        }

        if ($secure) {
            if (in_array(Role::ROLE_ADMIN, $this->getRoles(), true)) {
                throw new \Exception('No pueden ser eliminados los roles del administrador.');
            }
        }

        $this->roles->removeElement($role);

        return $this;
    }

    public function hasRole(Role $role): bool
    {
        return $this->roles->contains($role);
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @throws \Exception
     */
    public function register(UserPasswordHasherInterface $userPasswordHasher, Role $baseRol): static
    {
        $this->changePassword($userPasswordHasher);
        $this->deactivate();
        $this->addRole($baseRol);

        return $this;
    }

    public function changePassword(UserPasswordHasherInterface $userPasswordHasher): static
    {
        $pass = (is_null($this->password)) ? '' : $this->password;
        $encodePassword = $userPasswordHasher->hashPassword($this, $pass);
        $this->setPassword($encodePassword);

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function __toString(): string
    {
        return $this->getUsername();
    }

    /**
     * Can change this user roles.
     */
    public function blockRoles(): bool
    {
        return $this->isAdmin();
    }

    /**
     * Can change this user roles.
     */
    public function isSuperAdmin(): bool
    {
        return in_array(Role::ROLE_SUPER_ADMIN, $this->getRoles(), true);
    }

    /**
     * Can change this user roles.
     */
    public function isAdmin(): bool
    {
        return in_array(Role::ROLE_ADMIN, $this->getRoles(), true);
    }

    public function isDraftsman(): bool
    {
        return in_array(Role::ROLE_DRAFTSMAN, $this->getRoles(), true);
    }

    public function isClient(): bool
    {
        return in_array(Role::ROLE_CLIENT, $this->getRoles(), true);
    }

    public function isDirector(): bool
    {
        return in_array(Role::ROLE_DIRECTOR, $this->getRoles(), true);
    }

    public function isInvestor(): bool
    {
        return in_array(Role::ROLE_INVESTOR, $this->getRoles(), true);
    }
}
