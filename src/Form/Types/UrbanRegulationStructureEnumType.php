<?php

namespace App\Form\Types;

use App\Entity\Enums\UrbanRegulationStructure;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UrbanRegulationStructureEnumType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('class', UrbanRegulationStructure::class)
            ->setDefault('choices', static fn (Options $options): array => UrbanRegulationStructure::cases())
            ->setDefault('choice_label', UrbanRegulationStructure::getLabel())
            ->setDefault('choice_value', UrbanRegulationStructure::getValue());
        //            ->setDefault('placeholder', '-Seleccionar-');
    }

    public function getParent(): string
    {
        return EnumType::class;
    }
}
