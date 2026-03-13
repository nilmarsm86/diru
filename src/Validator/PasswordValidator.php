<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class PasswordValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof Password) {
            throw new UnexpectedTypeException($constraint, Password::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!is_string($value) && !$value instanceof \Stringable) {
            throw new \InvalidArgumentException('Valor no convertible a string');
        }

        $value = (string) $value;

        // Longitud mínima
        if (strlen($value) < 6) {
            $this->context->buildViolation('La contraseña debe tener como mínimo {{ limit }} caracteres')
                ->setParameter('{{ limit }}', '6')
                ->addViolation();
        }

        // Al menos una mayúscula
        if (false === preg_match('/[A-Z]/', $value)) {
            $this->context->buildViolation('La contraseña debe tener al menos {{ limit }} letra mayúscula')
                ->setParameter('{{ limit }}', '1')
                ->addViolation();
        }

        // Al menos un número
        if (false === preg_match('/[0-9]/', $value)) {
            $this->context->buildViolation('La contraseña debe tener al menos {{ limit }} número')
                ->setParameter('{{ limit }}', '1')
                ->addViolation();
        }

        // Al menos un carácter especial
        if (false === preg_match('/[!@#$%^&*\-_]/', $value)) {
            $this->context->buildViolation('La contraseña debe tener al menos {{ limit }} carácter especial')
                ->setParameter('{{ limit }}', '1')
                ->addViolation();
        }

        // Longitud máxima (opcional, descomenta si la necesitas)
        // if (strlen($value) > 20) {
        //     $this->context->buildViolation('La contraseña no puede tener más de {{ limit }} caracteres')
        //         ->setParameter('{{ limit }}', '20')
        //         ->addViolation();
        // }
    }
}
