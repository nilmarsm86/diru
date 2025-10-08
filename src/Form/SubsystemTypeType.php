<?php

namespace App\Form;

use App\Entity\SubsystemFunctionalClassification;
use App\Entity\SubsystemType;
use App\Form\Types\EntityPlusType;
use App\Form\Types\SubsystemFunctionalClassificationEnumType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
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
            ->add('subsystemSubTypes', LiveCollectionType::class, [
                'entry_type' => SubsystemSubTypeType::class,
                'button_delete_options' => [
                    'label_html' => true
                ],
                'error_bubbling' => false
            ])
            ->add('classification', SubsystemFunctionalClassificationEnumType::class, [
                'label' => 'Clasificación:',
            ]);
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
