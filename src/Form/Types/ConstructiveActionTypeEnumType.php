<?php

namespace App\Form\Types;

use App\Entity\Enums\ConstructiveActionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @template TData of array
 *
 * @extends AbstractType<array>
 */
class ConstructiveActionTypeEnumType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('class', ConstructiveActionType::class)
            ->setDefault('choices', static fn (Options $options): array => ConstructiveActionType::cases())
            ->setDefault('choice_label', ConstructiveActionType::getLabel())
            ->setDefault('choice_value', ConstructiveActionType::getValue());
    }

    public function getParent(): string
    {
        return EnumType::class;
    }
}
