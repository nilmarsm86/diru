<?php

namespace App\Form;

use App\DTO\RegistrationForm;
use App\Form\Types\PasswordToggleType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'attr' => [
                    'class' => 'form-control form-control-user no-border-left',
                    'placeholder' => 'Nombres*',
                    'autofocus' => true
                ]
            ])
            ->add('lastname', null, [
                'attr' => [
                    'class' => 'form-control form-control-user no-border-left',
                    'placeholder' => 'Apellidos*'
                ]
            ])
            ->add('identificationNumber', null, [
                'attr' => [
                    'class' => 'form-control form-control-user no-border-left',
                    'placeholder' => 'Carne de identidad*'
                ]
            ])
            ->add('phone', null, [
                'attr' => [
                    'class' => 'form-control form-control-user no-border-left',
                    'placeholder' => 'Teléfono'
                ]
            ])
            ->add('email', EmailType::class, [
                'attr' => [
                    'class' => 'form-control form-control-user no-border-left',
                    'placeholder' => 'Correo'
                ]
            ])
            ->add('username', null, [
                'attr' => [
                    'class' => 'form-control form-control-user no-border-left',
                    'placeholder' => 'Usuario*',
                    'aria-describedby' => 'usernameHelp'
                ]
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'label' => false
            ])

            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordToggleType::class,
                'first_options' => [
                    'attr' => [
                        'placeholder' => 'Contraseña*',
                        'autocomplete' => 'new-password',
                        'class' => 'form-control form-control-user no-border-left',
                        'style' => 'border-radius: var(--bs-border-radius); !important;border-top-left-radius: 0 !important;border-bottom-left-radius: 0 !important;'
                    ],
                ],
                'second_options' => [
                    'attr' => [
                        'placeholder' => 'Repetir Contraseña*',
                        'autocomplete' => 'new-password',
                        'class' => 'form-control form-control-user no-border-left',
                        'style' => 'border-radius: var(--bs-border-radius); !important;border-top-left-radius: 0 !important;border-bottom-left-radius: 0 !important;'
                    ],
                ],
                'invalid_message' => 'Las contraseñas no coinciden.'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RegistrationForm::class,
            'attr' => [
                'class' => 'user register',
                'novalidate' => 'novalidate'
            ]
        ]);
    }
}
