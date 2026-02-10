<?php

namespace App\Form;

use App\Entity\Local;
use App\Entity\SubSystem;
use App\Form\Types\LocalTypeEnumType;
use App\Form\Types\TechnicalStatusEnumType;
use App\Form\Types\TrixEditorType;
use App\Form\Types\UnitMeasurementFloatType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Range;

/**
 * @template TData of Local
 *
 * @extends AbstractType<Local>
 */
class LocalType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'label' => 'Nombre:',
                'attr' => [
                    'placeholder' => 'Nombre del local',
                ],
            ])
            ->add('number', TextType::class, [
                'label' => 'Número:',
                'attr' => [
                    'placeholder' => 'Número del local',
                    'min' => 1,
                ],
            ])
            ->add('type', LocalTypeEnumType::class, [
                'label' => 'Tipo de local:',
            ])
            ->add('height', UnitMeasurementFloatType::class, [
                'label' => 'Altura:',
                'unit' => 'm',
                'attr' => [
                    'placeholder' => 'Altura del local',
                    'data-controller' => 'positive-zero',
                    'min' => 0,
                ],
            ])
            ->add('impactHigherLevels', null, [
                'label' => 'Tiene impacto en niveles superiores:',
                'help' => 'Al marcar esta opción, la altura de este local tendrá impacto en niveles superiores.',
            ])
            ->add('comment', TrixEditorType::class, [
                'label' => false,
            ]);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($options): void {
            $this->onPreSetData($event, $options);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Local::class,
            'attr' => [
                'novalidate' => 'novalidate',
            ],
            'subSystem' => null,
            'reply' => false,
            'error_mapping' => [
                'enumType' => 'type',
                'enumTechnicalStatus' => 'technicalStatus',
            ],
        ]);

        $resolver->setAllowedTypes('subSystem', ['object']);
        $resolver->setAllowedTypes('reply', ['boolean']);
    }

    /**
     * @param array<mixed> $options
     */
    private function onPreSetData(FormEvent $event, array $options): void
    {
        /** @var Local $local */
        $local = $event->getData();
        $form = $event->getForm();

        $landArea = 0;
        $totalLocalsArea = 0;
        $subSystem = $options['subSystem'];
        if ($subSystem instanceof SubSystem) {
            $floor = $subSystem->getFloor();
            /** @var float $landArea */
            $landArea = $floor?->getBuilding()?->getMaxArea();
            if ((bool) $options['reply']) {
                /** @var float $landArea */
                $landArea = $floor?->getBuilding()?->getLandArea();
            }
            /** @var float $totalLocalsArea */
            $totalLocalsArea = $floor?->getTotalArea();
        }

        $leftArea = $landArea - $totalLocalsArea;

        $subSystem = $local->getSubSystem();
        if (is_null($local->getId()) && $leftArea > 1 && true === $subSystem?->notWallArea()) {
            --$leftArea;
        }

        if (null !== $local->getId()) {
            if ($local->getArea() > $leftArea) {
                $leftArea += $local->getArea();
            }
        }

        $constraints = [
            new Range(min: 0, max: $leftArea),
        ];

        $attr = [
            'min' => 1,
            'max' => $leftArea,
            'placeholder' => 'Área que ocupa el local',
        ];

        $form->add('area', UnitMeasurementFloatType::class, [
            'unit' => 'm<sup>2</sup>',
            'label' => 'Área:',
            'attr' => $attr,
            'constraints' => $constraints,
        ]);

        $subSystem = $local->getSubSystem();
        $floor = $subSystem?->getFloor();

        if (false === $floor?->isOriginal()
            || false === $subSystem?->isOriginal()
            || false === $local->isOriginal()
            || true === $local->inNewBuilding()
            || (true === $local->getId() && true === $local->hasReply())
        ) {
            $form->add('localConstructiveAction', LocalConstructiveActionType::class, [
                'required' => true,
                'error_bubbling' => false,
            ]);
        }

        $technicalStatusOptions = [
            'label' => 'Estado técnico:',
            'undefined_option' => $local->isOriginal(),
        ];

        $form->add('technicalStatus', TechnicalStatusEnumType::class, $technicalStatusOptions);
    }
}
