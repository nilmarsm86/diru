<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class UsernameValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!is_string($value) && !$value instanceof \Stringable) {
            throw new \InvalidArgumentException('Valor no convertible a string');
        }

        /* @var Username $constraint */
        if (!$constraint instanceof Username) {
            throw new UnexpectedTypeException($constraint, Username::class);
        }

        if ('' === $value) {
            return;
        }

        // strange characters
        if (false === preg_match('/^[a-z0-9_\-.]+$/', $value)) {
            $this->context->buildViolation('El nombre de usuario debe contener solo caracteres válidos.')
                ->addViolation();
        }

        /*// uppercase characters
        if (false !== preg_match('/[a-z]/', $value)) {
            $this->context->buildViolation('El nombre de usuario debe contener solo minúsculas.')
                ->addViolation();
        }*/
    }
}
