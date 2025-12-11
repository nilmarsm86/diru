<?php

namespace App\Form\Types;

use App\Entity\Enums\LocalType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @template TData of array
 *
 * @extends AbstractType<array>
 */
class LocalTypeEnumType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('class', LocalType::class)
            ->setDefault('choices', static fn (Options $options): array => LocalType::cases())
            ->setDefault('choice_label', LocalType::getLabel())
            ->setDefault('choice_value', LocalType::getValue());
    }

    public function getParent(): string
    {
        return EnumType::class;
    }
}
