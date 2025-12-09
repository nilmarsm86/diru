<?php

namespace App\Form\Types;

use App\Entity\Enums\ProjectState;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProjectStateEnumType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('class', ProjectState::class)
            ->setDefault('choices', static fn(Options $options): array => ProjectState::cases())
            ->setDefault('choice_label', ProjectState::getLabel())
            ->setDefault('choice_value', ProjectState::getValue());
    }

    public function getParent(): string
    {
        return EnumType::class;
    }
}
