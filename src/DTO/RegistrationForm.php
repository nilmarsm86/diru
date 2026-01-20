<?php

namespace App\DTO;

use App\Entity\User;
use App\Validator\Password;
use App\Validator\Username;
use Symfony\Component\Validator\Constraints as Assert;

final class RegistrationForm
{
    #[Assert\NotBlank(message: 'El nombre está vacío.')]
    #[Assert\NoSuspiciousCharacters]
    #[Assert\Regex(
        pattern: '/^[a-zA-Z,á,é,í,ó,ú,Á,É,Í,Ó,Ú,ñ,Ñ, ]+$/',
        message: 'El nombre debe contener solo letras.',
    )]
    public string $name;

    #[Assert\NotBlank(message: 'Los apellidos están vacíos.')]
    #[Assert\NoSuspiciousCharacters]
    #[Assert\Regex(
        pattern: '/^[a-zA-Z,á,é,í,ó,ú,Á,É,Í,Ó,Ú,ñ,Ñ, ]+$/',
        message: 'Los apellidos deben contener solo letras.',
    )]
    public string $lastname;

    #[Assert\NotBlank(message: 'Establezca el nombre de usuario.')]
    #[Assert\NoSuspiciousCharacters]
    #[Username]
    public string $username;

    #[Assert\IsTrue(message: 'Debe aprobar los términos y condiciones.')]
    public string $agreeTerms;

    #[Assert\NotBlank(message: 'La contraseña está vacía.')]
    #[Password]
    public string $plainPassword;

    #[Assert\NotBlank(message: 'El carnet de identidad está vacío.')]
    public string $identificationNumber = '';

    #[Assert\NotBlank(message: 'El correo está vacío.')]
    public string $email = '';

    #[Assert\NotBlank(message: 'El teléfono está vacío.')]
    public string $phone = '';

    public function toEntity(?User $user = null): User
    {
        if (is_null($user)) {
            $user = new User($this->name, $this->lastname, $this->username, $this->plainPassword, $this->identificationNumber, $this->phone, $this->email);
        }

        return $user;
    }
}
