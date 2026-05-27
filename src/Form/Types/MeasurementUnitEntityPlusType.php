<?php

namespace App\Form\Types;

use App\Entity\MeasurementUnit;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

/**
 * @template TData of array
 *
 * @extends AbstractType<array>
 */
class MeasurementUnitEntityPlusType extends AbstractType
{
    public function __construct(
        private readonly RouterInterface $router,
    ) {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('class', MeasurementUnit::class)
            ->setDefault('choice_label', fn (MeasurementUnit $measurementUnit): string => $measurementUnit->getName().' ('.$measurementUnit->getCode().')')
            ->setDefault('label', 'Unidad de Medida')
            ->setDefault('add', true)
            ->setDefault('add_title', 'Agregar unidad de medida')
            ->setDefault('add_id', 'modal-load')
            ->setDefault('add_url', $this->router->generate('app_measurement_unit_new', ['modal' => 'modal-load']));
    }

    public function getParent(): string
    {
        return EntityPlusType::class;
    }
}
