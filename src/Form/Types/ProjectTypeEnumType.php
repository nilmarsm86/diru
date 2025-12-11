<?php

namespace App\Form\Types;

use App\Entity\Enums\ProjectType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProjectTypeEnumType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('class', ProjectType::class)
            ->setDefault('choices', static fn (Options $options): array => ProjectType::cases())
            ->setDefault('choice_label', ProjectType::getLabel())
            ->setDefault('choice_value', ProjectType::getValue());
    }

    public function getParent(): string
    {
        return EnumType::class;
    }
}
