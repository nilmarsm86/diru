<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
final class Username extends Constraint
{
    public function __construct(
        public readonly string $message = 'El nombre de usuario contiene caracteres no válidos. Solo se permiten letras minúsculas, números, guiones, puntos y guiones bajos.',
        public readonly string $mode = 'strict',
        ?array $groups = null,
        mixed $payload = null,
    ) {
        parent::__construct([], $groups, $payload);
    }
}
