<?php

namespace App\Form\Types;

use App\Entity\Enums\SeparateConceptType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @template TData of array
 *
 * @extends AbstractType<array>
 */
class SeparateConceptTypeEnumType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('class', SeparateConceptType::class)
            ->setDefault('choices', static fn (Options $options): array => SeparateConceptType::cases())
            ->setDefault('choice_label', SeparateConceptType::getLabel())
            ->setDefault('choice_value', SeparateConceptType::getValue());
    }

    public function getParent(): string
    {
        return EnumType::class;
    }
}
