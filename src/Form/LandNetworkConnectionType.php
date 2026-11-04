<?php

namespace App\Form;

use App\Entity\LandNetworkConnection;
use App\Entity\NetworkConnection;
use App\Form\Types\EntityPlusType;
use App\Form\Types\NetworkConnectionEnumType;
use App\Form\Types\TechnicalStatusEnumType;
use App\Form\Types\TrixEditorType;
use App\Form\Types\UnitMeasurementFloatType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

/**
 * @template TData of LandNetworkConnection
 *
 * @extends AbstractType<LandNetworkConnection>
 */
class LandNetworkConnectionType extends AbstractType
{
    public function __construct(private readonly RouterInterface $router)
    {
    }

    /**
     * @param FormBuilderInterface<LandNetworkConnection|null> $builder
     * @param array<string, mixed>                             $options
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('explanation', TrixEditorType::class, [
            'label' => 'Explicación:',
            'required' => false,
        ])
            ->add('networkConnection', EntityPlusType::class, [
                'class' => NetworkConnection::class,
                'choice_label' => 'name',
                'label' => 'Red:',
                'placeholder' => '-Seleccione-',

                'add' => true,
                'add_title' => 'Agregar Conexion de Red:',
                'add_id' => 'modal-load',
                'add_url' => $this->router->generate('app_network_connection_new', ['modal' => 'modal-load']),
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
