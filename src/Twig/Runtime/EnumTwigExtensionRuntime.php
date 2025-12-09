<?php

namespace App\Twig\Runtime;

use Twig\Extension\RuntimeExtensionInterface;
use InvalidArgumentException;

class EnumTwigExtensionRuntime implements RuntimeExtensionInterface
{
    public function __construct()
    {
        // Inject dependencies if needed
    }

    /**
     * @param mixed $enum
     * @param mixed|null $value
     * @return string
     */
    public function getLabelFrom(mixed $enum, mixed $value = null): string
    {
        if (gettype($enum) === 'string') {
            $callback = [$enum, 'getLabelFrom'];
            assert(is_callable($callback));

            return call_user_func_array($callback, [$value]);
        } else {
            return $enum->getLabelFrom($enum);
        }
    }
}
