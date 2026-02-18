<?php

namespace App\Form;

use App\Entity\Building;
use App\Entity\Constructor;
use App\Entity\CorporateEntity;
use App\Entity\Draftsman;
use App\Entity\EnterpriseClient;
use App\Entity\IndividualClient;
use App\Entity\Project;
use App\Form\Types\EntityPlusType;
use App\Form\Types\MoneyPlusType;
use App\Repository\EnterpriseClientRepository;
use App\Repository\IndividualClientRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;
use Symfony\UX\LiveComponent\Form\Type\LiveCollectionType;

/**
 * @template TData of Building
 *
 * @extends AbstractType<Building>
 */
class BuildingType extends AbstractType
{
    public function __construct(
        private readonly RouterInterface $router,
        private readonly IndividualClientRepository $individualClientRepository,
        private readonly EnterpriseClientRepository $enterpriseClientRepository,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'label' => 'Nombre:',
                'attr' => [
                    'placeholder' => 'Nombre de la obra',
                ],
            ])
            ->add('population', null, [
                'label' => 'Cantidad de personas:',
                'attr' => [
                    'placeholder' => 'Cantidad de personas para la cual esta diseño',
                ],
                'empty_data' => 1,
            ])
            ->add('constructionAssemblyComment', null, [
                'label' => 'Comentario:',
                'attr' => [
                    'rows' => 1,
                ],
            ]);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($options): void {
            $this->onPreSetData($event, $options);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Building::class,
            'attr' => [
                'novalidate' => 'novalidate',
            ],
            'screen' => 'project', // building || project
            'urbanizationEstimate' => 0,
            'ptpEstimate' => 0,
        ]);
    }

    /**
     * @param array<mixed> $options
     */
    private function onPreSetData(FormEvent $event, array $options): void
    {
        /** @var Building $building */
        $building = $event->getData();
        $form = $event->getForm();
        $currency = 'CUP';
        $activeConstructor = null;
        $activeCorporateEntity = null;
        $projectPriceTechnicalPreparationAddConfig = [];
        $estimatedValueUrbanizationAddConfig = [];

        // TODO: y si ya de antemano se sabe que proyectista trabajara en la obra?
        if (null !== $building && null !== $building->getId()) {
            $form->add('draftsman', EntityType::class, [
                'mapped' => false,
                'class' => Draftsman::class,
                'placeholder' => '-Seleccione-',
                'label' => 'Proyectista:',
                'required' => false,
                'data' => $building->getActiveDraftsman(),
            ]);

            $project = $building->getProject();
            $currency = $project?->getCurrency();
            $currency = $currency?->getCode();
            $activeConstructor = $building->getActiveConstructor();
            $activeCorporateEntity = $building->getActiveCorporateEntity();

            $projectPriceTechnicalPreparationAddConfig = [
                'add' => true,
                'add_title' => 'Agregar estimado de proyecto y preparación técnica',
                'add_id' => 'modal-load',
                'add_url' => $this->router->generate('app_ptp_estimate_new', ['building' => (null !== $building) ? $building->getId() : 0, 'modal' => 'modal-load', 'screen' => $options['screen']]),
            ];

            $estimatedValueUrbanizationAddConfig = [
                'add' => true,
                'add_title' => 'Agregar estimado de urbanización',
                'add_id' => 'modal-load',
                'add_url' => $this->router->generate('app_urbanization_estimate_new', ['building' => (null !== $building) ? $building->getId() : 0, 'modal' => 'modal-load', 'screen' => $options['screen']]),
            ];
        }

        $form->add('constructor', EntityPlusType::class, [
            'class' => Constructor::class,
            'choice_label' => 'name',
            'label' => 'Constructora:',
            'mapped' => false,
            //                'query_builder' => $this->getOrganismQueryBuilder($options),

            'detail' => true,
            'detail_title' => 'Detalle de la constructora',
            'detail_id' => 'modal-load',
            'detail_url' => $this->router->generate('app_constructor_show', ['id' => 0, 'state' => 'modal']),

            'add' => true,
            'add_title' => 'Agregar Constructora',
            'add_id' => 'modal-load',
            'add_url' => $this->router->generate('app_constructor_new', ['modal' => 'modal-load', 'screen' => $options['screen']]),

            'required' => false,
            'data' => $activeConstructor,
        ]);

        $form->add('corporateEntity', EntityPlusType::class, [
            'class' => CorporateEntity::class,
            'choice_label' => 'name',
            'label' => 'Entidad corporativa:',
            'mapped' => false,
            //                'query_builder' => $this->getOrganismQueryBuilder($options),

            'detail' => true,
            'detail_title' => 'Detalle de la entidad corporativa de tipo constructora',
            'detail_id' => 'modal-load',
            'detail_url' => $this->router->generate('app_corporate_entity_show', ['id' => 0, 'state' => 'modal']),

            'add' => true,
            'add_title' => 'Agregar Entidad corporativa de tipo constructora',
            'add_id' => 'modal-load',
            'add_url' => $this->router->generate('app_corporate_entity_new', ['modal' => 'modal-load', 'screen' => $options['screen']]),

            'required' => false,
            'data' => $activeCorporateEntity,
        ]);

        $form->add('estimatedValueConstruction', MoneyType::class, [
            'label' => 'Construcción:',
            'attr' => [
                'placeholder' => '0',
                'min' => 0,
                'data-summation-values-target' => 'field',
                'data-currency-target' => 'field',
                'data-vecpppt' => true,
                'data-controller' => 'money',
                'readonly' => 'readonly',
            ],
            'empty_data' => 0,
            'required' => false,
            'currency' => $currency,
            //            'html5' => true,
            'input' => 'integer',
            'divisor' => 100,
            'grouping' => true,
            'mapped' => false,
            'data' => (null !== $building) ? $building->getEstimatedConstructionAndNetworkConnection() : 0,
        ])
            ->add('estimatedValueEquipment', MoneyType::class, [
                'label' => 'Equipos:',
                'attr' => [
                    'placeholder' => '0',
                    'min' => 0,
                    'data-summation-values-target' => 'field',
                    'data-currency-target' => 'field',
                    'data-controller' => 'money',
                ],
                'empty_data' => 0,
                'required' => false,
                'currency' => $currency,
                //                'html5' => true,
                'input' => 'integer',
                'divisor' => 100,
                'grouping' => true,
            ])
            ->add('estimatedValueOther', MoneyType::class, [
                'label' => 'Otros valores:',
                'attr' => [
                    'placeholder' => '0',
                    'min' => 0,
                    'data-summation-values-target' => 'field',
                    'data-currency-target' => 'field',
                    'data-controller' => 'money',
                ],
                'empty_data' => 0,
                'required' => false,
                'currency' => $currency,
                //                'html5' => true,
                'input' => 'integer',
                'divisor' => 100,
                'grouping' => true,
            ])
            ->add('approvedValueConstruction', MoneyType::class, [
                'label' => 'Valor aprobado de construcción:',
                'attr' => [
                    'placeholder' => '0',
                    'min' => 0,
                    'data-summation-values-target' => 'field',
                    'data-currency-target' => 'field',
                    'data-controller' => 'money',
                ],
                'empty_data' => 0,
                'required' => false,
                'currency' => $currency,
                //                'html5' => true,
                'input' => 'integer',
                'divisor' => 100,
                'grouping' => true,
            ])
            ->add('approvedValueEquipment', MoneyType::class, [
                'label' => 'Valor aprobado en equipos:',
                'attr' => [
                    'placeholder' => '0',
                    'min' => 0,
                    'data-summation-values-target' => 'field',
                    'data-currency-target' => 'field',
                    'data-controller' => 'money',
                ],
                'empty_data' => 0,
                'required' => false,
                'currency' => $currency,
                //                'html5' => true,
                'input' => 'integer',
                'divisor' => 100,
                'grouping' => true,
            ])
            ->add('approvedValueOther', MoneyType::class, [
                'label' => 'Otros valores aprobados:',
                'attr' => [
                    'placeholder' => '0',
                    'min' => 0,
                    'data-summation-values-target' => 'field',
                    'data-currency-target' => 'field',
                    'data-controller' => 'money',
                ],
                'empty_data' => 0,
                'required' => false,
                'currency' => $currency,
                //                'html5' => true,
                'input' => 'integer',
                'divisor' => 100,
                'grouping' => true,
            ])
            ->add('projectPriceTechnicalPreparation', MoneyPlusType::class, [
                'label' => 'Proy. y preparación técnica:',
                'attr' => [
                    'placeholder' => '0',
                    'min' => 0,
                    'data-summation-values-target' => 'field',
                    'data-currency-target' => 'field',
                    'data-vecpppt' => true,
                    'readonly' => 'readonly',
                    'data-controller' => 'money',
                    'data-type--money-plus-target' => 'field',
                ],
                'data' => (null !== $building && null !== $building->getId()) ? $building->getProjectTechnicalPreparationEstimateTotalPrice() : $options['ptpEstimate'],
                //                'empty_data' => 0,
                'required' => false,
                'currency' => $currency,
                //                'html5' => true,
                'input' => 'integer',
                'divisor' => 100,
                'mapped' => false,
                'grouping' => true,

                'list' => true,
                'list_title' => 'Listado de estimados de proyecto y preparación técnica',
                'list_id' => 'modal-load',
                'list_url' => $this->router->generate('app_ptp_estimate_index', [
                    'modal' => 'modal-load',
                    'screen' => $options['screen'],
                    'amount' => 100,
                    'building' => (null !== $building && null !== $building->getId()) ? $building->getId() : 0,
                ]),
            ] + $projectPriceTechnicalPreparationAddConfig)
            ->add('estimatedValueUrbanization', MoneyPlusType::class, [
                'label' => 'Urbanización:',
                'help' => 'Urbanizacion + conexiones de red externa.',
                'attr' => [
                    'placeholder' => '0',
                    'min' => 0,
                    'data-summation-values-target' => 'field',
                    'data-currency-target' => 'field',
                    'data-vecpppt' => true,
                    'readonly' => 'readonly',
                    'data-type--money-plus-target' => 'field',
                    'data-controller' => 'money',
                ],
                'data' => (null !== $building && null !== $building->getId()) ? $building->getEstimatedUrbanizationAndNetworkConnection() : $options['urbanizationEstimate'],
                //                'empty_data' => 0,
                'required' => false,
                'currency' => $currency,
                //                'html5' => true,
                'input' => 'integer',
                'divisor' => 100,
                'mapped' => false,
                'grouping' => true,

                'list' => true,
                'list_title' => 'Listado de estimados de urbanización',
                'list_id' => 'modal-load',
                'list_url' => $this->router->generate('app_urbanization_estimate_index', [
                    'modal' => 'modal-load',
                    'screen' => $options['screen'],
                    'amount' => 100,
                    'building' => (null !== $building && null !== $building->getId()) ? $building->getId() : 0,
                ]),
            ] + $estimatedValueUrbanizationAddConfig)
            ->add('constructionAssembly', MoneyType::class, [
                'label' => 'Precio:',
                'attr' => [
                    'placeholder' => '0',
                    'min' => 0,
                    'data-usd-currency-target' => 'field',
                    'data-controller' => 'money',
                ],
                'empty_data' => 0,
                'required' => false,
                'currency' => $currency,
                //                'html5' => true,
                'input' => 'integer',
                'divisor' => 100,
                'grouping' => true,
            ])
            ->add('landNetworkConnections', LiveCollectionType::class, [
                'entry_type' => LandNetworkConnectionType::class,
                'button_delete_options' => [
                    'label_html' => true,
                ],
                'error_bubbling' => false,
            ])
            ->add('range', RangeType::class, [
                'mapped' => false,
                'label' => '<a data-action="click->range#reset" title="Resetear rango" data-bs-toggle="tooltip"><svg viewBox="0 0 20 20" fill="currentColor" height="1em" width="1em" class="bi" aria-hidden="true"><path fill="currentColor" d="M19.295 12a.704.704 0 0 1 .705.709v3.204a.704.704 0 0 1-.7.709a.704.704 0 0 1-.7-.709v-1.125C16.779 17.844 13.399 20 9.757 20c-4.41 0-8.106-2.721-9.709-6.915a.71.71 0 0 1 .4-.917c.36-.141.766.04.906.405c1.4 3.662 4.588 6.01 8.403 6.01c3.371 0 6.52-2.182 7.987-5.154l-1.471.01a.704.704 0 0 1-.705-.704a.705.705 0 0 1 .695-.714zm-9.05-12c4.408 0 8.105 2.721 9.708 6.915a.71.71 0 0 1-.4.917a.697.697 0 0 1-.906-.405c-1.4-3.662-4.588-6.01-8.403-6.01c-3.371 0-6.52 2.182-7.987 5.154l1.471-.01a.704.704 0 0 1 .705.704a.705.705 0 0 1-.695.714L.705 8A.704.704 0 0 1 0 7.291V4.087c0-.392.313-.709.7-.709s.7.317.7.709v1.125C3.221 2.156 6.601 0 10.243 0"></path></svg></a>',
                'label_html' => true,
                'required' => false,
                'attr' => [
                    'class' => 'vecpppt',
                    'data-range-target' => 'range',
                    'min' => (null === $building) ? 0 : $building->getRangeMinPrice(),
                    'max' => (null === $building) ? 0 : $building->getRangeMaxPrice(),
                    //                    'step' => 1000
                ],
                'data' => (null === $building) ? 0 : $building->getRangePrice(),
                'row_attr' => [
                    'class' => 'mb-0',
                ],
            ])
            ->add('clientType', ChoiceType::class, [
                'label' => 'Tipo cliente:',
                'choices' => [
                    'Persona natural' => 'individual',
                    'Cliente Empresarial-Negocio' => 'enterprise',
                ],
                'mapped' => false,
                'expanded' => true,
                'multiple' => false,
                'data' => (is_null($building->getClient())) ? 'individual' : ($building->isIndividualClient($this->individualClientRepository) ? 'individual' : 'enterprise'),
                'attr' => [
                    'data-action' => 'change->visibility#toggle', // show or hide representative field
                ],
                'label_attr' => [
                    'class' => 'radio-inline',
                ],
            ])
            ->add('individualClient', EntityPlusType::class, [
                'class' => IndividualClient::class,
                'choice_label' => function (IndividualClient $individualClient) {
                    $person = $individualClient->getPerson();

                    return $person?->getFullName();
                },
                'mapped' => false,
                'label' => 'Persona natural',
                'data' => $building->getIndividualClient($this->individualClientRepository),

                'detail' => true,
                'detail_title' => 'Detalle del cliente individual',
                'detail_id' => 'modal-load',
                'detail_url' => $this->router->generate('app_individual_client_show', ['id' => 0, 'state' => 'modal']),

                'add' => true,
                'add_title' => 'Agregar cliente individual',
                'add_id' => 'modal-load',
                'add_url' => $this->router->generate('app_individual_client_new', ['modal' => 'modal-load']),
            ])
            ->add('enterpriseClient', EntityPlusType::class, [
                'class' => EnterpriseClient::class,
                'choice_label' => function (EnterpriseClient $enterpriseClient) {
                    $representative = $enterpriseClient->getRepresentative();
                    $corporateEntity = $enterpriseClient->getCorporateEntity();

                    return $corporateEntity?->getName().' ('.$representative?->getName().')';
                },
                'group_by' => fn (EnterpriseClient $enterpriseClient, int $key, string $value) => $enterpriseClient->getCorporateEntity(),
                'mapped' => false,
                'label' => 'Cliente empresarial-negocio (representante)',
                'data' => $building->getEnterpriseClient($this->enterpriseClientRepository),

                'detail' => true,
                'detail_title' => 'Detalle del cliente empresarial',
                'detail_id' => 'modal-load',
                'detail_url' => $this->router->generate('app_enterprise_client_show', ['id' => 0, 'state' => 'modal']),

                'add' => true,
                'add_title' => 'Agregar cliente empresarial-negocio',
                'add_id' => 'modal-load',
                'add_url' => $this->router->generate('app_enterprise_client_new', ['modal' => 'modal-load']),

                'modify' => true,
                'modify_title' => 'Detalle del cliente empresarial',
                'modify_id' => 'modal-load',
                'modify_url' => $this->router->generate('app_enterprise_client_edit', ['id' => 0, 'state' => 'modal', 'modal' => 'modal-load']),
            ]);
    }
}
