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
//                    'placeholder' => 'Nombre del representante'
                ]
            ])
            ->add('lastname', null, [
                'label' => 'Apellidos:',
                'attr' => [
//                    'placeholder' => 'Nombre del representante'
                ]
            ])
            ->add('identificationNumber', null, [
                'label' => 'Carnet de identidad:',
                'attr' => [
//                    'placeholder' => 'Carnet de identidad del representante'
                ]
            ])
            ->add('passport', null,[
                'label' => 'Pasaporte:',
                'attr' => [
//                    'placeholder' => 'Número de pasaporte del representante'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Person::class,
            'attr' => [
                'novalidate' => 'novalidate'
            ],
        ]);
    }
}
