<?php

namespace App\Form\Types;

use App\Entity\Enums\IteQuality;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @template TData of array
 *
 * @extends AbstractType<array>
 */
class IteQualityEnumType extends AbstractType
{
    /** @SuppressWarnings(PHPMD.UnusedFormalParameter) */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('class', IteQuality::class)
            ->setDefault('choices', static fn (Options $options): array => IteQuality::cases())
            ->setDefault('choice_label', IteQuality::getLabel())
            ->setDefault('choice_value', IteQuality::getValue());
    }

    public function getParent(): string
    {
        return EnumType::class;
    }
}
