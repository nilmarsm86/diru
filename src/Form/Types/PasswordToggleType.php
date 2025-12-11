<?php

namespace App\Form\Types;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @template TData of array
 *
 * @extends AbstractType<array>
 */
class PasswordToggleType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'toggle' => true,
            'use_toggle_form_theme' => false,
            'hidden_label' => null,
            'visible_label' => null,
        ]);
    }

    public function getParent(): string
    {
        return PasswordType::class;
    }
}
