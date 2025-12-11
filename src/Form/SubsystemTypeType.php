<?php

namespace App\Form;

use App\Entity\SubsystemType;
use App\Form\Types\SubsystemFunctionalClassificationEnumType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\LiveComponent\Form\Type\LiveCollectionType;

/**
 * @template TData of SubsystemType
 *
 * @extends AbstractType<SubsystemType>
 */
class SubsystemTypeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'label' => 'Nombre:',
                'attr' => [
                    'placeholder' => 'Nombre del tipo',
                ],
            ])
            ->add('subsystemTypeSubsystemSubTypes', LiveCollectionType::class, [
                'entry_type' => SubsystemTypeSubsystemSubTypeType::class,
                'button_delete_options' => [
                    'label_html' => true,
                ],
                'error_bubbling' => false,
            ])
            ->add('classification', SubsystemFunctionalClassificationEnumType::class, [
                'label' => 'Clasificación:',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SubsystemType::class,
            'attr' => [
                'novalidate' => 'novalidate',
            ],
            'error_mapping' => [
                'enumClassification' => 'classification',
            ],
        ]);
    }
}
