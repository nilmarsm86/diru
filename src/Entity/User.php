<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Validator\Username;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\Traits\StateTrait as StateTrait;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_USERNAME', fields: ['username'])]
#[DoctrineAssert\UniqueEntity(fields: ['username'], message: 'Ya existe un usuario con este nombre de usuario.')]
#[ORM\HasLifecycleCallbacks]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    use StateTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'El nombre del usuario no puede estar vacío.')]
    #[Assert\NotNull(message: 'El nombre del usaurio no puede ser nulo.')]
    #[Assert\NoSuspiciousCharacters]
    #[Assert\Regex(
        pattern: '/^[a-zA-Z,á,é,í,ó,ú,Á,É,Í,Ó,Ú,ñ,Ñ, ]+$/',
        message: 'El nombre del usuario debe contener solo letras.',
    )]
    private ?string $name = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'Los apellidos del usuario no pueden estar vacío.')]
    #[Assert\NotNull(message: 'Los apellidos del usaurio no pueden ser nulos.')]
    #[Assert\NoSuspiciousCharacters]
    #[Assert\Regex(
        pattern: '/^[a-zA-Z,á,é,í,ó,ú,Á,É,Í,Ó,Ú,ñ,Ñ, ]+$/',
        message: 'Los apellidos del usuario deben contener solo letras.',
    )]
    private ?string $lastname = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\NotBlank(message: 'Establezca el nombre de usuario.')]
    #[Assert\NotNull(message: 'El nombre de usuario no puede ser nulo.')]
    #[Assert\NoSuspiciousCharacters]
    #[Username]
    private ?string $username = null;

    #[ORM\ManyToMany(targetEntity: Role::class)]
    #[Assert\Count(
        min: 1,
        minMessage: 'Debe establecer al menos 1 rol para el usuario.',
    )]
    private Collection $roles;

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[Assert\NotBlank(message: 'Establezca la contraseña.')]
    #[Assert\NotNull(message: 'La contraseña no puede ser nula.')]
    private ?string $password = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'Escriba su carne de identidad.')]
    private ?string $identificationNumber = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $email = null;

    public function __construct(string $name, string $lastname, string $username, string $password, string $identificationNumber)
    {
        $this->name = $name;
        $this->lastname = $lastname;
        $this->username = $username;
        $this->password = $password;
        $this->roles = new ArrayCollection();
        $this->identificationNumber = $identificationNumber;
        $this->deactivate();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
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
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = [];
        foreach ($this->roles as $rol) {
            /** @var Role $rol */
            $roles[] = $rol->getName();
        }
        return array_unique($roles);
    }

    /**
     * @throws Exception
     */
    public function addRole(Role $role): static
    {
        /*if(!$this->isActive()){
            throw new Exception('No puede agregar rol a un usuario inactivo.');
        }*/

        if (!$this->roles->contains($role)) {
            $this->roles->add($role);
        }

        return $this;
    }

    /**
     * @throws Exception
     */
    public function removeRole(Role $role, bool $secure = true): static
    {
        if(!$this->isActive()){
            throw new Exception('No puede eliminar rol de un usuario inactivo.', 1);
        }

        if($role->getName() === Role::ROLE_CLIENT){
            throw new Exception('No puede ser eliminado el rol de cliente.');
        }

        if($secure){
            if(in_array(Role::ROLE_ADMIN, $this->getRoles())){
                throw new Exception('No pueden ser eliminados los roles del administrador.');
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
     * @throws Exception
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
        $encodePassword = $userPasswordHasher->hashPassword($this,$this->password);
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

    public function getFullName(): string
    {
        return $this->getName().' '.$this->getLastname();
    }

    public function __toString(): string
    {
        return $this->getUsername();
    }

    /**
     * Can change this user roles
     * @return bool
     */
    public function blockRoles(): bool
    {
        return $this->isAdmin();
    }

    /**
     * Can change this user roles
     * @return bool
     */
    public function isSuperAdmin(): bool
    {
        return in_array(Role::ROLE_SUPER_ADMIN, $this->getRoles());
    }

    /**
     * Can change this user roles
     * @return bool
     */
    public function isAdmin(): bool
    {
        return in_array(Role::ROLE_ADMIN, $this->getRoles());
    }

    /**
     * @return bool
     */
    public function isDraftsman(): bool
    {
        return in_array(Role::ROLE_DRAFTSMAN, $this->getRoles());
    }

    /**
     * @return bool
     */
    public function isClient(): bool
    {
        return in_array(Role::ROLE_CLIENT, $this->getRoles());
    }

    /**
     * @return bool
     */
    public function isDirector(): bool
    {
        return in_array(Role::ROLE_DIRECTOR, $this->getRoles());
    }

    /**
     * @return bool
     */
    public function isInvestor(): bool
    {
        return in_array(Role::ROLE_INVESTOR, $this->getRoles());
    }

    public function getIdentificationNumber(): ?string
    {
        return $this->identificationNumber;
    }

    public function setIdentificationNumber(string $identificationNumber): static
    {
        $this->identificationNumber = $identificationNumber;

        return $this;
    }

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
