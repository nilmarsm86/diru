<?php

namespace App\Form;

use App\Entity\Person;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PersonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'label' => 'Nombre:',
                'attr' => [
                    'placeholder' => 'Nombre'
                ]
            ])
            ->add('lastname', null, [
                'label' => 'Apellidos:',
                'attr' => [
                    'placeholder' => 'Apellidos'
                ]
            ])
            ->add('identificationNumber', null, [
                'label' => 'Carnet de identidad:',
                'attr' => [
                    'placeholder' => 'Carnet de identidad'
                ]
            ]);
        if ($options['passport']) {
            $builder->add('passport', null, [
                'label' => 'Pasaporte:',
                'attr' => [
                    'placeholder' => 'Pasaporte'
                ]
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Person::class,
            'attr' => [
                'novalidate' => 'novalidate'
            ],
            'passport' => true
        ]);

        $resolver->setAllowedTypes('passport', 'bool');
    }
}
