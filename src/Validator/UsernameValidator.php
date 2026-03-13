<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class UsernameValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof Username) {
            throw new UnexpectedTypeException($constraint, Username::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (false === is_string($value) && !$value instanceof \Stringable) {
            throw new \InvalidArgumentException('Valor no convertible a string');
        }

        $value = (string) $value;

        // Validar caracteres permitidos: minúsculas, números, guión bajo, guión, punto
        if (false === preg_match('/^[a-z0-9_\-\.]+$/', $value)) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }

        // Validar longitud mínima (opcional)
        if (strlen($value) < 3) {
            $this->context->buildViolation('El nombre de usuario debe tener al menos {{ limit }} caracteres')
                ->setParameter('{{ limit }}', '3')
                ->addViolation();
        }

        // Validar longitud máxima (opcional)
        if (strlen($value) > 50) {
            $this->context->buildViolation('El nombre de usuario no puede tener más de {{ limit }} caracteres')
                ->setParameter('{{ limit }}', '50')
                ->addViolation();
        }
    }
}
