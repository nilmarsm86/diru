<?php

namespace App\Form\Types;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UnitMeasurementType extends AbstractType
{

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('unit', '');
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
        return TextType::class;
    }
}
