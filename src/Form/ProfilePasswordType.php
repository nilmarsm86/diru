<?php

namespace App\Form;

use App\DTO\ProfilePasswordForm;
use App\Form\Types\PasswordToggleType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @template TData of ProfilePasswordForm
 *
 * @extends AbstractType<ProfilePasswordForm>
 */
class ProfilePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('oldPassword', PasswordToggleType::class, [
                'label_html' => true,
                'label' => '<strong>Contraseña actual: </strong>',
                'label_attr' => [
                    'class' => 'form-label col-sm-12',
                ],
                'attr' => [
                    'class' => 'form-control no-border-left',
                    'placeholder' => 'Constraseña actual',
                    'style' => 'border-radius: var(--bs-border-radius); !important;border-top-left-radius: 0 !important;border-bottom-left-radius: 0 !important;',
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordToggleType::class,
                'first_options' => [
                    'attr' => [
                        'autocomplete' => 'new-password',
                        'class' => 'form-control form-control-user no-border-left',
                        'placeholder' => 'Nueva contraseña',
                        'style' => 'border-radius: var(--bs-border-radius); !important;border-top-left-radius: 0 !important;border-bottom-left-radius: 0 !important;',
                    ],
                    'label_html' => true,
                    'label' => '<strong>Nueva contraseña:</strong>',
                    'label_attr' => [
                        'class' => 'form-label col-sm-12',
                    ],
                ],
                'second_options' => [
                    'attr' => [
                        'autocomplete' => 'new-password',
                        'class' => 'form-control form-control-user no-border-left',
                        'placeholder' => 'Repetir contraseña',
                        'style' => 'border-radius: var(--bs-border-radius); !important;border-top-left-radius: 0 !important;border-bottom-left-radius: 0 !important;',
                    ],
                    'label_html' => true,
                    'label' => '<strong>Repetir contraseña:</strong>',
                    'label_attr' => [
                        'class' => 'form-label col-sm-12',
                    ],
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProfilePasswordForm::class,
            'attr' => [
                'class' => 'profile',
                'novalidate' => 'novalidate',
            ],
        ]);
    }
}
