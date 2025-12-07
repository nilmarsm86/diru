<?php

namespace App\Form;

use App\Entity\Currency;
use App\Entity\Draftsman;
use App\Entity\EnterpriseClient;
use App\Entity\IndividualClient;
use App\Entity\Investment;
use App\Entity\Project;
use App\Form\Types\EntityPlusType;
use App\Form\Types\ProjectStateEnumType;
use App\Repository\EnterpriseClientRepository;
use App\Repository\IndividualClientRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\UX\LiveComponent\Form\Type\LiveCollectionType;
use App\Entity\Enums\ProjectType as EnumProjectType;

class ProjectType extends AbstractType
{
    public function __construct(
        private readonly RouterInterface            $router,
        private readonly IndividualClientRepository $individualClientRepository,
        private readonly EnterpriseClientRepository $enterpriseClientRepository,
    )
    {

    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'label' => 'Nombre:',
            ])
            ->add('investment', EntityPlusType::class, [
                'class' => Investment::class,
                'choice_label' => 'name',
                'label' => 'Datos de la Inversión:',

                'detail' => true,
                'detail_title' => 'Detalle de la Inversión',
                'detail_id' => 'modal-load',//'detail_investment_entity',
                'detail_url' => $this->router->generate('app_investment_show', ['id' => 0, 'state' => 'modal']),

                'add' => true,
                'add_title' => 'Agregar Inversión',
                'add_id' => 'modal-load',
                'add_url' => $this->router->generate('app_investment_new', ['modal' => 'modal-load']),
//                'query_builder' => $this->getInvestmentQueryBuilder($options),
                'constraints' => [
                    new Assert\NotBlank(message: 'Seleccione o cree la inversión a la cual pertenece el proyecto.')
                ]
            ])
            ->add('currency', EntityPlusType::class, [
                'class' => Currency::class,
                'label' => 'Moneda:',
                'choice_attr' => fn($choice, string $key, mixed $value) => ['data-code' => $choice->getCode()],
                'attr' => [
                    'data-currency-target' => 'select'
                ]
//                'constraints' => [
//                    new Assert\NotBlank(message: 'Seleccione la moneda de trabajo en el proyecto.')
//                ]
            ])
            ->add('comment', null, [
                'label' => false,
            ]);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void {
            $this->onPreSetData($event);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Project::class,
            'attr' => [
                'novalidate' => 'novalidate'
            ],
            'error_mapping' => [
                'enumType' => 'type',
            ],
        ]);
    }

//    /**
//     * @param array $options
//     * @return Closure
//     */
//    private function getInvestmentQueryBuilder(array $options): Closure
//    {
//        return function (EntityRepository $er) use ($options): QueryBuilder|array {
//            return $er->createQueryBuilder('i')
//                ->join('i.project', 'p')
//                ->orderBy('i.name');
//        };
//    }

    /**
     * @param FormEvent $event
     * @return void
     */
    private function onPreSetData(FormEvent $event): void
    {
        /** @var Project $project */
        $project = $event->getData();
        $form = $event->getForm();

        if ($project->getId()) {
            $form->add('stopReason', null, [
                'label' => false,
            ]);
            $form->add('state', ProjectStateEnumType::class, [
                'label' => 'Estado del proyecto:',
                'attr' => [
                    'data-visibility-by-select-target' => 'select'
                ]
            ]);
        } else {
            $moreAttrDraftsman = ['required' => false];
            $form->add('draftsman', EntityType::class, [
                    'mapped' => false,
                    'class' => Draftsman::class,
                    'placeholder' => '-Seleccione-',
                    'label' => 'Proyectista:'
                ] + $moreAttrDraftsman);
        }


        $moreAttr = [];
        if (!is_null($project->getContract())) {
            $moreAttr = [
                'constraints' => [
                    new Assert\Valid()
                ],
                'error_bubbling' => false
            ];
        }
        $form->add('contract', ContractType::class, [
                'required' => !is_null($project->getContract()),
            ] + $moreAttr);

        $form->add('clientType', ChoiceType::class, [
            'label' => 'Tipo cliente:',
            'choices' => [
                'Persona natural' => 'individual',
                'Cliente Empresarial-Negocio' => 'enterprise',
            ],
            'mapped' => false,
            'expanded' => true,
            'multiple' => false,
            'data' => (is_null($project->getClient())) ? 'individual' : ($project->isIndividualClient($this->individualClientRepository) ? 'individual' : 'enterprise'),
            'attr' => [
                'data-action' => 'change->visibility#toggle'//show or hide representative field
            ],
            'label_attr' => [
                'class' => 'radio-inline'
            ]
        ]);

        $form->add('individualClient', EntityPlusType::class, [
            'class' => IndividualClient::class,
            'choice_label' => function (IndividualClient $individualClient) {
                return $individualClient->getPerson()->getFullName();
            },
            'mapped' => false,
            'label' => 'Persona natural',
            'data' => $project->getIndividualClient($this->individualClientRepository),

            'detail' => true,
            'detail_title' => 'Detalle del cliente individual',
            'detail_id' => 'modal-load',
            'detail_url' => $this->router->generate('app_individual_client_show', ['id' => 0, 'state' => 'modal']),

            'add' => true,
            'add_title' => 'Agregar cliente individual',
            'add_id' => 'modal-load',
            'add_url' => $this->router->generate('app_individual_client_new', ['modal' => 'modal-load']),
        ]);
        $form->add('enterpriseClient', EntityPlusType::class, [
            'class' => EnterpriseClient::class,
            'choice_label' => function (EnterpriseClient $enterpriseClient) {
                return $enterpriseClient->getCorporateEntity()->getName();
            },
            'group_by' => fn(EnterpriseClient $enterpriseClient, int $key, string $value) => $enterpriseClient->getRepresentative(),
            'mapped' => false,
            'label' => 'Cliente empresarial-negocio',
            'data' => $project->getEnterpriseClient($this->enterpriseClientRepository),

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

        $form->add('type', ChoiceType::class, [
            'label' => 'Tipo de proyecto:',
            'expanded' => true,
            'multiple' => false,
            'placeholder' => null,
            'choices' => [
                EnumProjectType::getLabelFrom(EnumProjectType::Parcel) => EnumProjectType::Parcel,
                EnumProjectType::getLabelFrom(EnumProjectType::City) => EnumProjectType::City,
            ],
            'data' => (is_null($project->getType())) ? EnumProjectType::Parcel : (($project->getType()->value === EnumProjectType::Parcel->value) ? EnumProjectType::Parcel : EnumProjectType::City),
            'label_attr' => [
                'class' => 'radio-inline'
            ],
            'required' => false
        ]);

        $form->add('buildings', LiveCollectionType::class, [
            'entry_type' => BuildingType::class,
            'button_delete_options' => [
                'label_html' => true
            ],
            'constraints' => [
                new Assert\Count(
                    min: 1,
                    minMessage: 'Debe establecer al menos 1 obra para esta proyecto.',
                )
            ],
            'error_bubbling' => false,
        ]);
    }
}
