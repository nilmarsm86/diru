<?php

namespace App\Form;

use App\Entity\LandNetworkConnection;
use App\Entity\NetworkConnection;
use App\Form\Types\NetworkConnectionEnumType;
use App\Form\Types\TechnicalStatusEnumType;
use App\Form\Types\UnitMeasurementFloatType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @template TData of LandNetworkConnection
 *
 * @extends AbstractType<LandNetworkConnection>
 */
class LandNetworkConnectionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('explanation', null, [
            'label' => 'Explicación:',
            'attr' => [
                'rows' => 4,
            ],
        ])
            ->add('networkConnection', EntityType::class, [
                'class' => NetworkConnection::class,
                'choice_label' => 'name',
                'label' => 'Red:',
                'placeholder' => '-Seleccione-',
            ])
            ->add('type', NetworkConnectionEnumType::class, [
                'label' => 'Tipo de conexión:',
            ])
            ->add('longitude', UnitMeasurementFloatType::class, [
                'label' => 'Longitud:',
                'unit' => 'm',
                'attr' => [
                    'min' => 0,
                ],
                'empty_data' => 0,
            ])
            ->add('technicalStatus', TechnicalStatusEnumType::class, [
                'label' => 'Estado técnico:',
            ])
            ->add('landNetworkConnectionConstructiveAction', LandNetworkConnectionConstructiveActionType::class, [
                'required' => true,
                'error_bubbling' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => LandNetworkConnection::class,
            'error_mapping' => [
                'enumTechnicalStatus' => 'technicalStatus',
                'enumType' => 'type',
            ],
        ]);
    }
}
