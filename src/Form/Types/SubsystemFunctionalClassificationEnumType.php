<?php

namespace App\Form\Types;

use App\Entity\Enums\CorporateEntityType;
use App\Entity\Enums\LocalType;
use App\Entity\Enums\SubsystemFunctionalClassification;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SubsystemFunctionalClassificationEnumType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('class', SubsystemFunctionalClassification::class)
            ->setDefault('choices', static fn (Options $options): array => $options['class']::cases())
            ->setDefault('choice_label', SubsystemFunctionalClassification::getLabel())
            ->setDefault('choice_value', SubsystemFunctionalClassification::getValue())
        ;
    }

    public function getParent(): string
    {
        return EnumType::class;
    }
}
