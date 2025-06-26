<?php

namespace App\Form;

use App\Entity\Floor;
use App\Entity\Local;
use App\Form\Types\LocalTechnicalStatusEnumType;
use App\Form\Types\LocalTypeEnumType;
use App\Form\Types\UnitMeasurementType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LocalType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'label' => 'Nombre:',
                'attr' => [
                    'placeholder' => 'Nombre del local'
                ]
            ])
            ->add('number', null, [
                'label' => 'Número:',
                'attr' => [
                    'placeholder' => 'Número del local'
                ]
            ])
            ->add('area', UnitMeasurementType::class, [
                'unit' => 'm<sup>2</sup>',
                'label' => "Área:",
                'attr' => [
                    'min' => 0,
                    'max' => 10000
                ]
            ])
            ->add('type', LocalTypeEnumType::class, [
                'label' => 'Tipo de local:',
            ])
            ->add('height', null, [
                'label' => 'Altura:',
            ])
            ->add('technicalStatus', LocalTechnicalStatusEnumType::class, [
                'label' => 'Estado técnico:',
            ])
//            ->add('type2', EnumType::class, [
//                'class' => \App\Entity\Enums\LocalType::class,
//            ])
//            ->add('floor', EntityType::class, [
//                'class' => Floor::class,
//                'choice_label' => 'id',
//            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Local::class,
            'attr' => [
                'novalidate' => 'novalidate'
            ]
        ]);
    }
}
