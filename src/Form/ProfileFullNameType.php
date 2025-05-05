<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfileFullNameType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'label_html' => true,
                'label' => '<strong>Nombres: </strong>',
                'label_attr' => [
                    'class' => 'form-label col-sm-12'
                ],
                'attr' => [
                    'class' => 'form-control no-border-left'
                ]
            ])
            ->add('lastname', null, [
                'label_html' => true,
                'label' => '<strong>Apellidos: </strong>',
                'label_attr' => [
                    'class' => 'form-label col-sm-12'
                ],
                'attr' => [
                    'class' => 'form-control no-border-left'
                ]
            ])
            ->add('identificationNumber', null, [
                'label_html' => true,
                'label' => '<strong>Carne de identidad: </strong>',
                'label_attr' => [
                    'class' => 'form-label col-sm-12'
                ],
                'attr' => [
                    'class' => 'form-control no-border-left',
                ]
            ])
            ->add('phone', null, [
                'label_html' => true,
                'label' => '<strong>Teléfono: </strong>',
                'label_attr' => [
                    'class' => 'form-label col-sm-12'
                ],
                'attr' => [
                    'class' => 'form-control no-border-left',
                ]
            ])
            ->add('email', EmailType::class, [
                'label_html' => true,
                'label' => '<strong>Correo: </strong>',
                'label_attr' => [
                    'class' => 'form-label col-sm-12'
                ],
                'attr' => [
                    'class' => 'form-control no-border-left',
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'attr' => [
                'class' => 'profile_name',
                'novalidate' => 'novalidate'
            ]
        ]);
    }
}
