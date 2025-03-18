<?php

namespace App\DTO;

use App\Entity\User;
use App\Validator\Password;
use App\Validator\Username;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\NotBlank;

final class RegistrationForm
{
    #[NotBlank(message: 'Escriba su nombre.')]
    public string $name;

    #[NotBlank(message: 'Escriba sus apellidos.')]
    public string $lastname;

    #[NotBlank(message: 'Escriba el nombre de usuario.')]
    #[Username]
    public string $username;

    #[IsTrue(message: 'Debe aprobar los términos.')]
    public string $agreeTerms;

    #[NotBlank(message: 'Escriba la contraseña.')]
    #[Password]
    public string $plainPassword;

    /**
     * @param User|null $user
     * @return User
     */
    public function toEntity(?User $user = null): User
    {
        if(is_null($user)){
            return new User($this->name, $this->lastname, $this->username, $this->plainPassword);
        }

        return $user;
    }
}