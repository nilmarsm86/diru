<?php

namespace App\Twig\Runtime;

use Twig\Extension\RuntimeExtensionInterface;

class EnumTwigExtensionRuntime implements RuntimeExtensionInterface
{
    public function getLabelFrom(mixed $enum, mixed $value = null): string
    {
        //        if (gettype($enum) === 'string') {
        $callback = [$enum, 'getLabelFrom'];
        assert(is_callable($callback));

        if ('string' !== gettype($enum)) {
            $value = $enum;
        }
        /** @var string $callbackResult */
        $callbackResult = call_user_func_array($callback, [$value]);

        return $callbackResult;
    }
}
