<?php

namespace App\Form;

use App\Entity\Land;
use App\Form\Types\UnitMeasurementFloatType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
     * @param array<mixed> $options
     * @return void
     */
    private function onPreSetData(FormEvent $event, array $options): void
    {
        /** @var Land $land */
        $land = $event->getData();
        $form = $event->getForm();

        $disabled = [];
        if (!is_null($land) && $land->getId() && !$options['building']->isNew()) {
            $disabled = ['disabled' => true, 'readonly' => true];
        }

        $form
            ->add('landArea', UnitMeasurementFloatType::class, [
                'unit' => 'm<sup>2</sup>',
                'label' => "Área de terreno:",
                'attr' => [
                        'min' => 0
                    ] + $disabled
            ])
            ->add('occupiedArea', UnitMeasurementFloatType::class, [
                'unit' => 'm<sup>2</sup>',
                'label' => "Área ocupada:",
                'attr' => [
                        'min' => 0
                    ] + $disabled,
                'required' => false
            ])
            ->add('perimeter', UnitMeasurementFloatType::class, [
                'unit' => 'm',
                'label' => "Perímetro:",
                'attr' => [
                        'min' => 0
                    ] + $disabled,
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
            ]);
    }
}
