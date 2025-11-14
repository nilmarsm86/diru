<?php

namespace App\Form\Types;

use App\Entity\Enums\CorporateEntityType;
use App\Entity\Enums\TechnicalStatus;
use App\Entity\Enums\LocalType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TechnicalStatusEnumType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('undefined_option', true)
            ->setDefault('class', TechnicalStatus::class)
//            ->setDefault('choices', static fn (Options $options): array => $options['class']::cases())
            ->setDefault('choices', static function (Options $options): array {
                if($options['undefined_option']){
                    return $options['class']::cases();
                }

                $cases = $options['class']::cases();
                unset($cases[1]);
                return $cases;
            })
            ->setDefault('choice_label', TechnicalStatus::getLabel())
            ->setDefault('choice_value', TechnicalStatus::getValue());
    }

    public function getParent(): string
    {
        return EnumType::class;
    }
}
