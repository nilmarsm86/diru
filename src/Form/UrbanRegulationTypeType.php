<?php

namespace App\Form;

use App\Entity\UrbanRegulationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @template TData of UrbanRegulationType
 * @extends AbstractType<UrbanRegulationType>
 */
class UrbanRegulationTypeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'label' => 'Nombre:',
                'attr' => [
                    'placeholder' => 'Nombre del tipo de regulación urbana',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UrbanRegulationType::class,
            'attr' => [
                'novalidate' => 'novalidate'
            ],
        ]);
    }
}
