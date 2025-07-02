<?php

namespace App\Form;

use App\Entity\Constructor;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConstructorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'label' => 'Nombre:',
                'attr' => [
                    'placeholder' => 'Nombre de la constructora'
                ]
            ])
            ->add('code', null, [
                'label' => 'Código:',
                'attr' => [
                    'placeholder' => 'Código de la constructora'
                ]
            ])
            ->add('country', ChoiceType::class, [
                'label' => 'País:',
                'placeholder' => '-Seleccione-',
                'choices' => [
                    'Cuba' => 'CU'
                ],
                'data' => 'CU'
            ])
            ->add('logo', FileType::class, [
                'label' => 'Logo:',
                'required' => false
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Constructor::class,
            'attr' => [
                'novalidate' => 'novalidate'
            ]
        ]);
    }
}
