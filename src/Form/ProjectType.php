<?php

namespace App\Form;

use App\Entity\Client;
use App\Entity\Draftsman;
use App\Entity\EnterpriseClient;
use App\Entity\IndividualClient;
use App\Entity\Investment;
use App\Entity\Person;
use App\Entity\Project;
use App\Form\Types\EntityPlusType;
use App\Form\Types\ProjectStateEnumType;
use App\Form\Types\ProjectTypeEnumType;
use Closure;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\UX\LiveComponent\Form\Type\LiveCollectionType;

class ProjectType extends AbstractType
{
    public function __construct(private readonly RouterInterface $router)
    {

    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'label' => 'Nombre:',
            ])
            ->add('type', ProjectTypeEnumType::class, [
                'label' => 'Tipo de proyecto:',
//                'data' => \App\Entity\Enums\ProjectType::Parcel
                'constraints' => [
                    new Assert\NotBlank(message: 'Seleccione el tipo de proyecto.'),
                ]
            ])
            //solo para modificar(cambiar el estado)
//            ->add('state', ProjectStateEnumType::class, [
//                'label' => 'Estado del proyecto:',
//            ])
            ->add('isStopped', CheckboxType::class, [
                'label' => 'Esta detenido:',
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'data-action' => 'change->visibility#toggle'//show or hide representative field
                ],
                'data' => false
            ])
            //solo se muestra si el estado que se selecciona es el del parado
            ->add('stopReason', null, [
                'label' => 'Razón de parar el proyecto:',
            ])
//            ->add('hasOccupiedArea', null, [
//                'label' => 'Tiene área ocupada:',
//            ])
            ->add('comment', null, [
                'label' => 'Comentar:',
            ])
            ->add('clientType', ChoiceType::class, [
                'label' => 'Tipo cliente:',
                'choices' => [
                    'Persona natural' => 'individual_client',
                    'Cliente Empresarial' => 'enterprise_client',
                ],
                'mapped' => false,
                'expanded' => true,
                'multiple' => false,
                'data' => 'individual_client',
                'attr' => [
                    'data-action' => 'change->visibility#toggle'//show or hide representative field
                ],
            ])
            //hacer la iteracion por los clientes
//            ->add('client', HiddenType::class, [
////                'class' => Client::class,
////                'choice_label' => 'id',
//            ])
            ->add('individualClient', EntityType::class, [
                'class' => IndividualClient::class,
                'choice_label' => function (IndividualClient $individualClient) {
                    return $individualClient->getPerson()->getFullName();
                },
                'mapped' => false,
                'label' => 'Persona natural',
//                'placeholder' => '-Seleccione-'
            ])
            ->add('enterpriseClient', EntityType::class, [
                'class' => EnterpriseClient::class,
                'choice_label' => 'representative',
                'mapped' => false,
                'label' => 'Cliente empresarial',
//                'placeholder' => '-Seleccione-'
            ])
            ->add('investment', EntityPlusType::class, [
                'class' => Investment::class,
                'choice_label' => 'name',
                'label' => 'Inversión:',
                'detail' => true,
                'detail_title' => 'Detalle de la Inversión',
                'detail_id' => 'detail_investment_entity',
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
            ->add('buildings', LiveCollectionType::class, [
                'entry_type' => BuildingType::class,
                'button_delete_options' => [
                    'label_html' => true
                ],
                'constraints' => [
                    new Assert\Count(
                        min: 1,
                        minMessage: 'Debe establecer al menos 1 obra para esta proyecto.',
                    )
                ]
            ]);;
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
//                'client' => 'individualClient',
                //'client' => 'enterpriseClient',
            ],
        ]);
    }

    /**
     * @param array $options
     * @return Closure
     */
    private function getInvestmentQueryBuilder(array $options): Closure
    {
        return function (EntityRepository $er) use ($options): QueryBuilder|array {
            return $er->createQueryBuilder('i')
                ->join('i.project', 'p')
//                ->where('i.project is null')
                ->orderBy('i.name');
        };
    }
}
