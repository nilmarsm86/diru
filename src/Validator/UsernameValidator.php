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

        if ('' == $value) {
            return;
        }

        // strange characters
        if (!preg_match('/^[a-zA-Z0-9_\-.]+$/', $value)) {
            $this->context->buildViolation('El nombre de usuario debe contener solo caracteres válidos.')
                ->addViolation();
        }

        // uppercase characters
        if (preg_match('/[A-Z]/', $value)) {
            $this->context->buildViolation('El nombre de usuario debe contener solo minúsculas.')
                ->addViolation();
        }

        /*// TODO: implement the validation here
        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ value }}', $value)
            ->addViolation()
        ;*/
    }
}
