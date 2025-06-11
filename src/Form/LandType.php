<?php

namespace App\Form;

use App\Entity\Land;
use App\Form\Types\UnitMeasurementType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\LiveComponent\Form\Type\LiveCollectionType;

class LandType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('landArea', UnitMeasurementType::class, [
                'unit' => 'm<sup>2</sup>',
                'label' => "Area de terreno:",
                'attr' => [
                    'min' => 0
                ]
            ])
            ->add('occupiedArea', UnitMeasurementType::class, [
                'unit' => 'm<sup>2</sup>',
                'label' => "Area ocupada:",
                'attr' => [
                    'min' => 0
                ]
            ])
            ->add('perimeter', UnitMeasurementType::class, [
                'unit' => 'm',
                'label' => "Perímetro:",
                'attr' => [
                    'min' => 0
                ]
            ])
            ->add('photo', FileType::class, [
                'label' => "Foto:",
                'required' => false
            ])
            ->add('microlocalization', FileType::class, [
                'label' => "Microlocalización:",
                'required' => false
            ])
            ->add('floor', NumberType::class, [
                'label' => "Plantas:"
            ])
            ->add('landNetworkConnections', LiveCollectionType::class, [
                'entry_type' => LandNetworkConnectionType::class,
                'button_delete_options' => [
                    'label_html' => true
                ],
                'error_bubbling' => false,
            ])
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void {
            $this->onPreSetData($event);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Land::class,
            'attr' => [
                'novalidate' => 'novalidate'
            ],
        ]);
    }

    /**
     * @param FormEvent $event
     * @return void
     */
    private function onPreSetData(FormEvent $event): void
    {
        /** @var Land $land */
        $land = $event->getData();
        $form = $event->getForm();

        $moreAttr = [];
        if (!is_null($land) && $land->getId()) {
            $moreAttr = ['data' => $land->getOccupiedArea() > 0 ? ['1'] : ['0']];
        }

        $form->add('isOccupied', ChoiceType::class, [
            'label' => 'Area ocupada:',
            'mapped' => false,
            'multiple' => true,
            'expanded' => true,
            'choices' => [
                '&nbsp;' => '1'
            ],
            'label_html' => true,
            'attr' => [
                'data-action' => 'change->visibility#toggle'//show or hide representative field
            ],
            'choice_attr' => [
                '&nbsp;' => ['data-live--land-form-target' => 'check']
            ],
        ] + $moreAttr);
    }
}
