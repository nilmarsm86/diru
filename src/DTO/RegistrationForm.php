<?php

namespace App\DTO;

use App\Entity\User;
use App\Validator\Password;
use App\Validator\Username;
use Symfony\Component\Validator\Constraints as Assert;

final class RegistrationForm
{
    #[Assert\NotBlank(message: 'El nombre del usuario no puede estar vacío.')]
    #[Assert\NoSuspiciousCharacters]
    #[Assert\Regex(
        pattern: '/^[a-zA-Z,á,é,í,ó,ú,Á,É,Í,Ó,Ú,ñ,Ñ, ]+$/',
        message: 'El nombre del usuario debe contener solo letras.',
    )]
    public string $name;

    #[Assert\NotBlank(message: 'Los apellidos del usuario no pueden estar vacío.')]
    #[Assert\NoSuspiciousCharacters]
    #[Assert\Regex(
        pattern: '/^[a-zA-Z,á,é,í,ó,ú,Á,É,Í,Ó,Ú,ñ,Ñ, ]+$/',
        message: 'Los apellidos del usuario deben contener solo letras.',
    )]
    public string $lastname;

    #[Assert\NotBlank(message: 'Establezca el nombre de usuario.')]
    #[Assert\NoSuspiciousCharacters]
    #[Username]
    public string $username;

    #[Assert\IsTrue(message: 'Debe aprobar los términos.')]
    public string $agreeTerms;

    #[Assert\NotBlank(message: 'Escriba la contraseña.')]
    #[Password]
    public string $plainPassword;

    #[Assert\NotBlank(message: 'Escriba su carne de identidad.')]
    public string $identificationNumber;

    public ?string $email = null;
    public ?string $phone = null;

    /**
     * @param User|null $user
     * @return User
     */
    public function toEntity(?User $user = null): User
    {
        if(is_null($user)){
            $user =  new User($this->name, $this->lastname, $this->username, $this->plainPassword, $this->identificationNumber);
            $user->setEmail($this->email);
            $user->setPhone($this->phone);
        }

        return $user;
    }
}