<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @template TData of User
 *
 * @extends AbstractType<User>
 */
class ProfileFullNameType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('person', PersonType::class, [
                'passport' => false,
            ])
            ->add('phone', null, [
                'label_html' => true,
                'label' => '<strong>Teléfono: </strong>',
                'label_attr' => [
                    'class' => 'form-label col-sm-12',
                ],
                'attr' => [
                    'class' => 'form-control no-border-left',
                    'placeholder' => 'Teléfono del usuario',
                ],
            ])
            ->add('email', EmailType::class, [
                'label_html' => true,
                'label' => '<strong>Correo: </strong>',
                'label_attr' => [
                    'class' => 'form-label col-sm-12',
                ],
                'attr' => [
                    'class' => 'form-control no-border-left',
                    'placeholder' => 'Correo del usuario',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'attr' => [
                'class' => 'profile_name',
                'novalidate' => 'novalidate',
            ],
        ]);
    }
}
