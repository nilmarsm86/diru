<?php

namespace App\Form\Types;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UnitMeasurementFloatType extends AbstractType
{

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('unit', '')
            ->setDefault('html5', true);

    }

    /**
     * {@inheritDoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['unit'] = $options['unit'];
    }

    public function getParent(): string
    {
        return NumberType::class;
    }
}
