<?php

namespace App\Form;

use App\Entity\Building;
use App\Entity\Floor;
use App\Entity\Local;
use App\Form\Types\LocalTechnicalStatusEnumType;
use App\Form\Types\LocalTypeEnumType;
use App\Form\Types\UnitMeasurementType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Range;

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
                    'placeholder' => 'Número del local',
                    'min' => 1,
                ]
            ])
            ->add('type', LocalTypeEnumType::class, [
                'label' => 'Tipo de local:',
            ])
            ->add('height', UnitMeasurementType::class, [
                'label' => 'Altura:',
                'unit' => 'm',
                'attr' => [
                    'placeholder' => 'Altura del local'
                ],
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

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($options): void {
            $this->onPreSetData($event, $options);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Local::class,
            'attr' => [
                'novalidate' => 'novalidate'
            ],
            'floor' => null,
            'error_mapping' => [
                'enumType' => 'type',
                'enumTechnicalStatus' => 'technicalStatus',
            ],
        ]);

        $resolver->setAllowedTypes('floor', ['object']);
    }

    /**
     * @param FormEvent $event
     * @param array $options
     * @return void
     */
    private function onPreSetData(FormEvent $event, array $options): void
    {
        /** @var Local $local */
        $local = $event->getData();
        $form = $event->getForm();

        $landArea = $options['floor']->getBuilding()->getMaxArea();
        $totalLocalsArea = $options['floor']->getTotalFloorArea();
        $leftArea = $landArea - $totalLocalsArea;
        if($local && $local->getId()){
            $leftArea = $local->getArea();
        }
        $constraints = [
            new Range(min: 1, max: $leftArea),
        ];

        $form->add('area', UnitMeasurementType::class, [
            'unit' => 'm<sup>2</sup>',
            'label' => "Área:",
            'attr' => [
                'min' => 1,
                'max' => $leftArea,
                'placeholder' => 'Área que ocupa el local'
            ],
            'constraints' => $constraints,
        ]);
    }
}
