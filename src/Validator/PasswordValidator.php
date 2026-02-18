<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class PasswordValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (null === $value) {
            $value = '';
        }

        if (!is_string($value) && !$value instanceof \Stringable) {
            throw new \InvalidArgumentException('Valor no convertible a string');
        }

        /* @var Password $constraint */

        if (!$constraint instanceof Password) {
            throw new UnexpectedTypeException($constraint, Password::class);
        }

        if ('' === $value) {
            return;
        }

        if (false === preg_match('/^.{6,}$/', $value)) {
            $this->context->buildViolation('La contraseña debe tener como mínimo {{ limit }} caracteres')
                ->setParameter('{{ limit }}', '6')
                ->addViolation();
        }

        if (false === preg_match('/^(?=.*[A-Z]).{6,}$/', $value)) {
            $this->context->buildViolation('La contraseña debe tener como mínimo {{ limit }} caracter en mayúscula')
                ->setParameter('{{ limit }}', '1')
                ->addViolation();
        }

        if (false === preg_match('/^(?=.*[0-9])(?=.*[A-Z]).{6,20}$/', $value)) {
            $this->context->buildViolation('La contraseña debe tener como mínimo {{ limit }} número.')
                ->setParameter('{{ limit }}', '1')
                ->addViolation();
        }

        if (false === preg_match('/^(?=.*[!@#$%^&*-])(?=.*[0-9])(?=.*[A-Z]).{6,20}$/', $value)) {
            $this->context->buildViolation('La contraseña debe tener como mínimo {{ limit }} caracter especial.')
                ->setParameter('{{ limit }}', '1')
                ->addViolation();
        }
    }
}
