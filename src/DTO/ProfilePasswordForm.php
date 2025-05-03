<?php

namespace App\DTO;

use App\Entity\User;
use App\Validator\Password;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;

final class ProfilePasswordForm
{
    #[UserPassword(message: 'Contraseña actual incorrecta.')]
    public string $oldPassword;

    #[Assert\NotBlank(message: 'Establezca la nueva contraseña.')]
    #[Assert\NotNull(message: 'La nueva contraseña no puede ser nula.')]
    #[Password]
    public string $plainPassword;

    /**
     * @param User|null $user
     * @return User
     */
    public function toEntity(?User $user): User
    {
        $user->setPassword($this->plainPassword);
        return $user;
    }
}