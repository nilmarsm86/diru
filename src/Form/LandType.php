<?php

namespace App\Form;

use App\Entity\Land;
use App\Form\Types\UnitMeasurementType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\LiveComponent\Form\Type\LiveCollectionType;

class LandType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($options): void {
            $this->onPreSetData($event, $options);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Land::class,
            'attr' => [
                'novalidate' => 'novalidate'
            ],
            'building' => null,
        ]);
    }

    /**
     * @param FormEvent $event
     * @return void
     */
    private function onPreSetData(FormEvent $event, array $options): void
    {
        /** @var Land $land */
        $land = $event->getData();
        $form = $event->getForm();

//        $moreAttr = [];
        $disabled = [];
        if (!is_null($land) && $land->getId() && !$options['building']->isNew()) {
//            $moreAttr = ['data' => $land->getOccupiedArea() > 0 ? ['1'] : ['0']];
            $disabled = ['disabled' => true, 'readonly' => true];
        }

        $form
            ->add('landArea', UnitMeasurementType::class, [
                'unit' => 'm<sup>2</sup>',
                'label' => "Área de terreno:",
                'attr' => [
                        'min' => 0
                    ] + $disabled
            ])
            ->add('occupiedArea', UnitMeasurementType::class, [
                'unit' => 'm<sup>2</sup>',
                'label' => "Área ocupada:",
                'attr' => [
                        'min' => 0
                    ] + $disabled,
                'required' => false
            ])
            ->add('perimeter', UnitMeasurementType::class, [
                'unit' => 'm',
                'label' => "Perímetro:",
                'attr' => [
                        'min' => 0
                    ] + $disabled,
//                'empty_data' => 0,
                'required' => false
            ])
            ->add('photo', FileType::class, [
                'label' => "Foto:",
                'required' => false,
                'attr' => [] + $disabled
            ])
            ->add('microlocalization', FileType::class, [
                'label' => "Microlocalización:",
                'required' => false,
                'attr' => [] + $disabled
            ])
            ->add('floor', ChoiceType::class, [
                'label' => "Plantas:",
                'placeholder' => '-Seleccinar-',
                'choices' => array_combine(range(1, 50), range(1, 50)),
                'required' => false,
                'attr' => [] + $disabled
            ])
            ->add('landNetworkConnections', LiveCollectionType::class, [
                'entry_type' => LandNetworkConnectionType::class,
                'button_delete_options' => [
                    'label_html' => true
                ],
                'error_bubbling' => false,
            ]);

//        $form->add('isOccupied', ChoiceType::class, [
//                'label' => 'Area ocupada:',
//                'mapped' => false,
//                'multiple' => true,
//                'expanded' => true,
//                'choices' => [
//                    '&nbsp;' => '1'
//                ],
//                'label_html' => true,
//                'attr' => [
//                    'data-action' => 'change->visibility#toggle'//show or hide representative field
//                ],
//                'choice_attr' => [
//                    '&nbsp;' => ['data-live--land-form-target' => 'check']
//                ],
//            ] + $moreAttr);
    }
}
