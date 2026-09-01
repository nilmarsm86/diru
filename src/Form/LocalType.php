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
    /**
     * @param FormBuilderInterface<Local|null> $builder
     * @param array<string, mixed>             $options
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
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

        $leftArea = $this->calculateAvailableArea($local, $options);

        $min = $local->isWallType() ? 0 : 1;

        $form->add('area', UnitMeasurementFloatType::class, [
            'unit' => 'm<sup>2</sup>',
            'label' => 'Área:',
            'attr' => [
                'min' => $min,
                'max' => $leftArea,
                'placeholder' => 'Área que ocupa el local',
            ],
            'constraints' => [
                new Range(min: 0, max: $leftArea),
            ],
            'help' => sprintf('Desde %sm<sup>2</sup> hasta %sm<sup>2</sup>', $min, $leftArea),
            'help_html' => true,
        ]);

        if ($this->shouldShowConstructiveAction($local)) {
            $form->add('localConstructiveAction', LocalConstructiveActionType::class, [
                'required' => true,
                'error_bubbling' => false,
            ]);
        }

        $form->add('technicalStatus', TechnicalStatusEnumType::class, [
            'label' => 'Estado técnico:',
            'undefined_option' => $local->isOriginal(),
        ]);
    }
    //    private function onPreSetData(FormEvent $event, array $options): void
    //    {
    //        /** @var Local $local */
    //        $local = $event->getData();
    //        $form = $event->getForm();
    //
    //        $landArea = 0;
    //        $totalLocalsArea = 0;
    //        $subSystem = $options['subSystem'];
    //        if ($subSystem instanceof SubSystem) {
    //            $floor = $subSystem->getFloor();
    //            /** @var float $landArea */
    //            $landArea = $floor?->getBuilding()?->getMaxArea();
    //            if ((bool) $options['reply']) {
    //                /** @var float $landArea */
    //                $landArea = $floor?->getBuilding()?->getLandArea();
    //            }
    //            /** @var float $totalLocalsArea */
    //            $totalLocalsArea = $floor?->getTotalArea();
    //        }
    //
    //        $leftArea = $landArea - $totalLocalsArea;
    //
    //        $subSystem = $local->getSubSystem();
    //        if (is_null($local->getId()) && $leftArea > 1 && true === $subSystem?->notWallArea()) {
    //            --$leftArea;
    //        }
    //
    //        if (null !== $local->getId()) {
    //            //            if ($local->getArea() > $leftArea) {
    //            $leftArea += (null !== $local->getArea()) ? $local->getArea() : 0;
    //            //            }
    //        }
    //
    //        $constraints = [
    //            new Range(min: 0, max: $leftArea),
    //        ];
    //
    //        $attr = [
    //            'min' => 1,
    //            'max' => $leftArea,
    //            'placeholder' => 'Área que ocupa el local',
    //        ];
    //
    //        if ($local->isWallType()) {
    //            $attr['min'] = 0;
    //        }
    //
    //        $form->add('area', UnitMeasurementFloatType::class, [
    //            'unit' => 'm<sup>2</sup>',
    //            'label' => 'Área:',
    //            'attr' => $attr,
    //            'constraints' => $constraints,
    //            'help' => 'Desde '.$attr['min'].'m<sup>2</sup> hasta '.$leftArea.'m<sup>2</sup>',
    //            'help_html' => true,
    //        ]);
    //
    //        $subSystem = $local->getSubSystem();
    //        $floor = $subSystem?->getFloor();
    //
    //        if (false === $floor?->isOriginal()
    //            || false === $subSystem?->isOriginal()
    //            || false === $local->isOriginal()
    //            || true === $local->inNewBuilding()
    //            || (true === $local->getId() && true === $local->hasReply())
    //        ) {
    //            $form->add('localConstructiveAction', LocalConstructiveActionType::class, [
    //                'required' => true,
    //                'error_bubbling' => false,
    //            ]);
    //        }
    //
    //        $technicalStatusOptions = [
    //            'label' => 'Estado técnico:',
    //            'undefined_option' => $local->isOriginal(),
    //        ];
    //
    //        $form->add('technicalStatus', TechnicalStatusEnumType::class, $technicalStatusOptions);
    //    }

    /**
     * @param array<mixed> $options
     */
    private function calculateAvailableArea(Local $local, array $options): float
    {
        $subSystem = $options['subSystem'] ?? $local->getSubSystem();
        if (!$subSystem instanceof SubSystem) {
            return 0.0;
        }

        $floor = $subSystem->getFloor();
        $building = $floor?->getBuilding();

        $landArea = (bool) ($options['reply'] ?? false)
            ? ($building?->getLandArea() ?? 0.0)
            : ($building?->getMaxArea() ?? 0.0);

        $totalLocalsArea = $floor?->getTotalArea() ?? 0.0;
        $leftArea = $landArea - $totalLocalsArea;

        // Ajuste cuando es un local nuevo y el sub-sistema no cuenta muro
        if (null === $local->getId() && $leftArea > 1 && true === $subSystem->notWallArea()) {
            --$leftArea;
        }

        // Si el local ya existe, devolvemos el área que él mismo ocupa
        if (null !== $local->getId()) {
            $leftArea += $local->getArea() ?? 0.0;
        }

        return max(0.0, $leftArea);
    }

    private function shouldShowConstructiveAction(Local $local): bool
    {
        $subSystem = $local->getSubSystem();
        $floor = $subSystem?->getFloor();

        return false === $floor?->isOriginal()
            || false === $subSystem?->isOriginal()
            || false === $local->isOriginal()
            || true === $local->inNewBuilding()
            || (null !== $local->getId() && true === $local->hasReply());
    }
}
