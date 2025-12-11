<?php

namespace App\Form\Types;

use App\Entity\Enums\NetworkConnectionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NetworkConnectionEnumType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('class', NetworkConnectionType::class)
            ->setDefault('choices', static fn (Options $options): array => NetworkConnectionType::cases())
            ->setDefault('choice_label', NetworkConnectionType::getLabel())
            ->setDefault('choice_value', NetworkConnectionType::getValue());
    }

    public function getParent(): string
    {
        return EnumType::class;
    }
}
