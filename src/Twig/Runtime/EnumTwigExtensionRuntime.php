<?php

namespace App\Twig\Runtime;

use Twig\Extension\RuntimeExtensionInterface;

class EnumTwigExtensionRuntime implements RuntimeExtensionInterface
{
    public function __construct()
    {
        // Inject dependencies if needed
    }

    public function getLabelFrom(string $enum, $value): string
    {
        return call_user_func_array([$enum, 'getLabelFrom'], [$value]);
    }
}
