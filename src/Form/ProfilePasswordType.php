<?php

namespace App\Form;

use App\DTO\ProfilePasswordForm;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfilePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('oldPassword', PasswordType::class, [
                'label_html' => true,
                'label' => '<strong>Contraseña actual: </strong>',
                'label_attr' => [
                    'class' => 'form-label col-sm-12'
                ],
                'attr' => [
                    'class' => 'form-control no-border-left'
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => [
                    'attr' => [
                        'autocomplete' => 'new-password',
                        'class' => 'form-control form-control-user no-border-left',
                    ],
                    'label_html' => true,
                    'label' => '<strong>Nueva contraseña:</strong>',
                    'label_attr' => [
                        'class' => 'form-label col-sm-12'
                    ]
                ],
                'second_options' => [
                    'attr' => [
                        'autocomplete' => 'new-password',
                        'class' => 'form-control form-control-user no-border-left',
                    ],
                    'label_html' => true,
                    'label' => '<strong>Repetir contraseña:</strong>',
                    'label_attr' => [
                        'class' => 'form-label col-sm-12'
                    ]
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProfilePasswordForm::class,
            'attr' => [
                'class' => 'profile',
            ]
        ]);
    }
}
