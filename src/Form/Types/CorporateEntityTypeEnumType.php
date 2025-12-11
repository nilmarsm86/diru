<?php

namespace App\Form\Types;

use App\Entity\Enums\CorporateEntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @template TData of array
 *
 * @extends AbstractType<array>
 */
class CorporateEntityTypeEnumType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('class', CorporateEntityType::class)
            ->setDefault('choices', static fn (Options $options): array => CorporateEntityType::cases())
            ->setDefault('choice_label', CorporateEntityType::getLabel())
            ->setDefault('choice_value', CorporateEntityType::getValue());
    }

    public function getParent(): string
    {
        return EnumType::class;
    }
}
