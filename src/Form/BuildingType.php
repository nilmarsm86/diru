<?php

namespace App\Form;

use App\Entity\Building;
use App\Entity\Constructor;
use App\Entity\Draftsman;
use App\Entity\Project;
use App\Form\Types\EntityPlusType;
use App\Form\Types\MoneyPlusType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
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
    public function __construct(private readonly RouterInterface $router)
    {
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
            'data' => (null !== $building) ? $building->getPrice() : 0,
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
                'list_url' => $this->router->generate('app_ptp_estimate_index', ['modal' => 'modal-load', 'screen' => $options['screen'], 'amount' => 100]),
            ] + $projectPriceTechnicalPreparationAddConfig)
            ->add('estimatedValueUrbanization', MoneyPlusType::class, [
                'label' => 'Urbanización:',
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
                'data' => (null !== $building && null !== $building->getId()) ? $building->getUrbanizationEstimateTotalPrice() : $options['urbanizationEstimate'],
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
                'list_url' => $this->router->generate('app_urbanization_estimate_index', ['modal' => 'modal-load', 'screen' => $options['screen'], 'amount' => 100]),
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
                'label' => false,
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
        ;
    }
}
