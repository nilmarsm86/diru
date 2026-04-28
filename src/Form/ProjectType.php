<?php

namespace App\Form;

use App\Entity\Currency;
use App\Entity\Draftsman;
use App\Entity\EnterpriseClient;
use App\Entity\Enums\ProjectType as EnumProjectType;
use App\Entity\IndividualClient;
use App\Entity\Investment;
use App\Entity\Project;
use App\Form\Types\EntityPlusType;
use App\Form\Types\ProjectStateEnumType;
use App\Form\Types\TrixEditorType;
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

/**
 * @template TData of Project
 *
 * @extends AbstractType<Project>
 */
class ProjectType extends AbstractType
{
    public function __construct(
        private readonly RouterInterface $router,
        private readonly IndividualClientRepository $individualClientRepository,
        private readonly EnterpriseClientRepository $enterpriseClientRepository,
    ) {
    }

    /**
     * @param FormBuilderInterface<Project|null> $builder
     * @param array<string, mixed>               $options
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
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
                'detail_id' => 'modal-load', // 'detail_investment_entity',
                'detail_url' => $this->router->generate('app_investment_show', ['id' => 0, 'state' => 'modal']),

                'add' => true,
                'add_title' => 'Agregar Inversión',
                'add_id' => 'modal-load',
                'add_url' => $this->router->generate('app_investment_new', ['modal' => 'modal-load']),
                'constraints' => [
                    new Assert\NotBlank(message: 'Seleccione o cree la inversión a la cual pertenece el proyecto.'),
                ],
            ])
            ->add('currency', EntityPlusType::class, [
                'class' => Currency::class,
                'label' => 'Moneda:',
                'choice_attr' => fn (Currency $choice, string $key, mixed $value) => ['data-code' => $choice->getCode()],
                'attr' => [
                    'data-currency-target' => 'select',
                ],
                'add' => true,
                'add_title' => 'Agregar Moneda',
                'add_id' => 'modal-load',
                'add_url' => $this->router->generate('app_currency_new', ['modal' => 'modal-load']),
            ])
            ->add('comment', TrixEditorType::class, [
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
                'novalidate' => 'novalidate',
            ],
            'error_mapping' => [
                'enumType' => 'type',
            ],
        ]);
    }

    /** @SuppressWarnings(PHPMD.UnusedFormalParameter) */
    private function onPreSetData(FormEvent $event): void
    {
        /** @var Project $project */
        $project = $event->getData();
        $form = $event->getForm();

        if (null !== $project->getId()) {
            $form->add('stopReason', TrixEditorType::class, ['label' => false]);
            $form->add('state', ProjectStateEnumType::class, [
                'label' => 'Estado del proyecto:',
                'attr' => ['data-visibility-by-select-target' => 'select'],
            ]);
        } else {
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
                'label_attr' => ['class' => 'radio-inline'],
                'required' => false,
            ]);
        }

        $form->add('draftsman', EntityType::class, [
            'mapped' => false,
            'class' => Draftsman::class,
            'placeholder' => '-Seleccione-',
            'label' => 'Proyectista:',
            'required' => false,
            'data' => $project->getActiveDraftsman(),
        ]);

        $moreAttr = [];
        if (null !== $project->getContract()) {
            $moreAttr = [
                'constraints' => [new Assert\Valid()],
                'error_bubbling' => false,
            ];
        }
        $form
            ->add('contract', ContractType::class, [
                'required' => !is_null($project->getContract()),
            ] + $moreAttr)
            ->add('clientType', ChoiceType::class, [
                'label' => 'Tipo cliente:',
                'choices' => [
                    'Persona natural' => 'individual',
                    'Cliente Empresarial-Negocio' => 'enterprise',
                ],
                'mapped' => false,
                'expanded' => true,
                'multiple' => false,
                'data' => (is_null($project->getClient())) ? 'individual' : ($project->isIndividualClient($this->individualClientRepository) ? 'individual' : 'enterprise'),
                'attr' => ['data-action' => 'change->visibility#toggle'], // show or hide representative field
                'label_attr' => ['class' => 'radio-inline'],
            ])
            ->add('individualClient', EntityPlusType::class, [
                'class' => IndividualClient::class,
                'choice_label' => function (IndividualClient $individualClient) {
                    $person = $individualClient->getPerson();

                    return $person?->getFullName();
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
    }
}
