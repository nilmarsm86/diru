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
//                'label_html' => true,
                'label' => 'Teléfono:',
//                'label_attr' => [
//                    'class' => 'form-label col-sm-12'
//                ],
                'attr' => [
//                    'class' => 'form-control no-border-left',
                    'placeholder' => 'Teléfono del representante'
                ]
            ])
            ->add('email', EmailType::class, [
//                'label_html' => true,
                'label' => 'Correo:',
//                'label_attr' => [
//                    'class' => 'form-label col-sm-12'
//                ],
                'attr' => [
//                    'class' => 'form-control no-border-left',
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
