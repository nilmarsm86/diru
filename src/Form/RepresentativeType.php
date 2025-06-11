<?php

namespace App\Form;

use App\Entity\Representative;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RepresentativeType extends AbstractType
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
            ])
            ->add('passport', null,[
                'label' => 'Pasaporte:',
                'attr' => [
                    'placeholder' => 'Pasaporte'
                ]
            ])
            ->add('phone', null, [
                'label' => 'Teléfono:',
                'attr' => [
                    'placeholder' => 'Teléfono del representante'
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => 'Correo:',
                'attr' => [
                    'placeholder' => 'Correo del representante'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Representative::class,
            'attr' => [
                'novalidate' => 'novalidate'
            ],
        ]);
    }
}
