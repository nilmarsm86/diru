<?php

namespace App\Form;

use App\Entity\Building;
use App\Entity\ConstructiveAction;
use App\Entity\CorporateEntity;
use App\Entity\Draftsman;
use App\Entity\EnterpriseClient;
use App\Entity\IndividualClient;
use App\Entity\Project;
use App\Form\Types\EntityPlusType;
use App\Form\Types\MoneyPlusType;
use App\Form\Types\SimpleArrayTextareaType;
use App\Repository\EnterpriseClientRepository;
use App\Repository\IndividualClientRepository;
use App\Service\BuildingValuationService;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
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
        private readonly BuildingValuationService $buildingValuationService,
    ) {
    }

    /**
     * @param FormBuilderInterface<Building|null> $builder
     * @param array<string, mixed>                $options
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
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
            ])
            ->add('constructionRealValueComment', null, [
                'label' => 'Comentario:',
                'attr' => [
                    'rows' => 1,
                ],
            ])
            ->add('activity', EntityType::class, [
                'class' => ConstructiveAction::class,
                'choice_label' => 'name',
                'label' => 'Actividad de Obra:',
                'placeholder' => '-Seleccione-',
                'group_by' => fn (ConstructiveAction $constructiveAction, int $key, string $value) => $constructiveAction->getType()::getLabelFrom($constructiveAction->getType()),
            ])
            ->add('objects', SimpleArrayTextareaType::class, [
                'label' => 'Objetos de obra',
                'separator' => ',',
                'attr' => [
                    'rows' => 3,
                    'placeholder' => 'ej: objeto de obra1, objeto de obra2, objeto de obra3',
                ],
                'help' => 'Ingrese las etiquetas separadas por comas',
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
            'justValueEstimate' => 0,
        ]);
    }

    /**
     * @param array<mixed> $options
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    private function onPreSetData(FormEvent $event, array $options): void
    {
        /** @var Building $building */
        $building = $event->getData();
        $form = $event->getForm();
        $currency = 'CUP';
        $activeCorporateEntity = null;
        $projectPriceTechnicalPreparationAddConfig = [];
        $estimatedValueUrbanizationAddConfig = [];
        $estimatedJustValueAddConfig = [];

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
            $estimatedJustValueAddConfig = [
                'add' => true,
                'add_title' => 'Agregar valor estimado ajustado',
                'add_id' => 'modal-load',
                'add_url' => $this->router->generate('app_just_value_estimate_new', ['building' => (null !== $building) ? $building->getId() : 0, 'modal' => 'modal-load', 'screen' => $options['screen']]),
            ];
        }

        $form
            ->add('corporateEntity', EntityPlusType::class, [
                'class' => CorporateEntity::class,
                'choice_label' => 'name',
                'label' => 'Entidad corporativa de tipo constructora:',
                'mapped' => false,
                'query_builder' => $this->getCorporateEntityConstructor(),
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
            ])
            ->add('estimatedValueConstruction', MoneyType::class, [
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
                'input' => 'integer',
                'divisor' => 100,
                'grouping' => true,
                'mapped' => false,
                'data' => (null !== $building) ? $this->buildingValuationService->getEstimatedConstructionAndNetworkConnection($building) : 0,
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
                'required' => false,
                'currency' => $currency,
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
                'data' => (null !== $building && null !== $building->getId()) ? $this->buildingValuationService->getEstimatedUrbanizationAndNetworkConnection($building) : $options['urbanizationEstimate'],
                'required' => false,
                'currency' => $currency,
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
            ->add('estimatedJustValue', MoneyPlusType::class, [
                'label' => 'Valor estimado ajustado (<strong class="multiply">$'.((null !== $building && null !== $building->getId()) ? number_format($building->getEstimatedAdjustValue() / 100, 2) : number_format(0, 2)).'</strong>):',
                'label_html' => true,
                'attr' => [
                    'placeholder' => '0',
                    'min' => 0,
                    'readonly' => 'readonly',
                    'data-type--money-plus-target' => 'field',
                    'data-controller' => 'money',
                ],
                'data' => (null !== $building && null !== $building->getId()) ? $building->getJustValueEstimateTotalPrice() : $options['justValueEstimate'],
                'required' => false,
                'currency' => $currency,
                'input' => 'integer',
                'divisor' => 100,
                'mapped' => false,
                'grouping' => true,
                'list' => true,
                'list_title' => 'Listado de valores estimados ajustados',
                'list_id' => 'modal-load',
                'list_url' => $this->router->generate('app_just_value_estimate_index', [
                    'modal' => 'modal-load',
                    'screen' => $options['screen'],
                    'amount' => 100,
                    'building' => (null !== $building && null !== $building->getId()) ? $building->getId() : 0,
                ]),
            ] + $estimatedJustValueAddConfig)
            ->add('coefficient', NumberType::class, [
                'label' => 'Coeficiente de ajuste:',
                'required' => false,
                'attr' => [
                    'data-estimate-presupposition-target' => 'field',
                ],
            ])
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
            ])
            ->add('constructionRealValue', MoneyType::class, [
                'label' => 'Valor real:',
                'attr' => [
                    'placeholder' => '0',
                    'min' => 0,
                    'data-usd-currency-target' => 'field',
                    'data-controller' => 'money',
                ],
                'empty_data' => 0,
                'required' => false,
                'currency' => $currency,
                'input' => 'integer',
                'divisor' => 100,
                'grouping' => true,
            ]);
    }

    private function getCorporateEntityConstructor(): \Closure
    {
        return fn (EntityRepository $er): QueryBuilder => $er->createQueryBuilder('ce')
            ->andWhere('ce.type = '.\App\Entity\Enums\CorporateEntityType::Constructor->value)
            ->orWhere('ce.type = '.\App\Entity\Enums\CorporateEntityType::ClientAndConstructor->value)
            ->orderBy('ce.name', 'ASC');
    }
}
