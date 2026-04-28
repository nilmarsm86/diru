<?php

namespace App\Form;

use App\Entity\MeasurementUnit;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @template TData of MeasurementUnit
 *
 * @extends AbstractType<MeasurementUnit>
 */
class MeasurementUnitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('code')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MeasurementUnit::class,
        ]);
    }
}
