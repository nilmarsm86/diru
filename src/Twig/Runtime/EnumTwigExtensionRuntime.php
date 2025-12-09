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

            /** @var string $callbackResult */
            $callbackResult = call_user_func_array($callback, [$value]);
            return $callbackResult;
        } else {
            if(is_object($enum)){
                return $enum->getLabelFrom($enum);
            }

            return '';
        }
    }
}
