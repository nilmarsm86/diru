<?php

namespace App\Form;

use App\Entity\SubsystemType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\LiveComponent\Form\Type\LiveCollectionType;

class SubsystemTypeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'label' => 'Nombre:',
                'attr' => [
                    'placeholder' => 'Nombre del tipo de subsistema'
                ]
            ])
            ->add('subTypes', LiveCollectionType::class, [
                'entry_type' => SubsystemSubTypeType::class,
                'button_delete_options' => [
                    'label_html' => true
                ],
                'error_bubbling' => false
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SubsystemType::class,
            'attr' => [
                'novalidate' => 'novalidate'
            ],
        ]);
    }
}
