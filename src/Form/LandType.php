<?php

namespace App\Form;

use App\Entity\Building;
use App\Entity\Land;
use App\Form\Types\UnitMeasurementFloatType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @template TData of Land
 *
 * @extends AbstractType<Land>
 */
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
                'novalidate' => 'novalidate',
            ],
            'building' => null,
        ]);
    }

    /**
     * @param array<mixed> $options
     */
    private function onPreSetData(FormEvent $event, array $options): void
    {
        /** @var Land $land */
        $land = $event->getData();
        $form = $event->getForm();

        $disabled = [];
        $building = $options['building'];
        if ($building instanceof Building) {
            if (null !== $land && null !== $land->getId() && false === $building->isNew()) {
                $disabled = ['disabled' => true, 'readonly' => true];
            }
        }

        $form
            ->add('landArea', UnitMeasurementFloatType::class, [
                'unit' => 'm<sup>2</sup>',
                'label' => 'Área de terreno:',
                'attr' => [
                    'min' => 0,
                ] + $disabled,
            ])
            ->add('occupiedArea', UnitMeasurementFloatType::class, [
                'unit' => 'm<sup>2</sup>',
                'label' => 'Área ocupada:',
                'attr' => [
                    'min' => 0,
                ] + $disabled,
                'required' => false,
            ])
            ->add('perimeter', UnitMeasurementFloatType::class, [
                'unit' => 'm',
                'label' => 'Perímetro:',
                'attr' => [
                    'min' => 0,
                ] + $disabled,
                'required' => false,
            ])
            ->add('picture', FileType::class, [
                'label' => 'Foto:',
                'required' => false,
                'attr' => [
                    'accept' => '.jpg,image/jpeg,.jpeg,.png,image/png',
                ] + $disabled,
                'mapped' => false,
            ])
            ->add('micro', FileType::class, [
                'label' => 'Microlocalización:',
                'required' => false,
                'attr' => [
                    'accept' => '.pdf,application/pdf',
                ] + $disabled,
                'mapped' => false,
            ])
            ->add('floor', ChoiceType::class, [
                'label' => 'Plantas:',
                'placeholder' => '-Seleccinar-',
                'choices' => array_combine(range(1, 50), range(1, 50)),
                'required' => false,
                'attr' => [] + $disabled,
            ]);
    }
}
