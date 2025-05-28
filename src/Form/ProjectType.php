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
            ->add('hasOccupiedArea', null, [
                'label' => 'Tiene área ocupada:',
            ])
//            ->add('registerAt', null, [
//                'widget' => 'single_text',
//            ])
//            ->add('stoppedAt', null, [
//                'widget' => 'single_text',
//            ])
//            ->add('canceledAt', null, [
//                'widget' => 'single_text',
//            ])
//            ->add('initiatedAt', null, [
//                'widget' => 'single_text',
//            ])
//            ->add('completedDiagnosticStatusAt', null, [
//                'widget' => 'single_text',
//            ])
//            ->add('urbanRregulationAt', null, [
//                'widget' => 'single_text',
//            ])
//            ->add('designAt', null, [
//                'widget' => 'single_text',
//            ])
            ->add('comment', null, [
                'label' => 'Comentar:',
            ])
//            ->add('draftsmans', EntityType::class, [
//                'class' => Draftsman::class,
//                'choice_label' => 'name',
////                'multiple' => true,
//            ])
            ->add('clientType', ChoiceType::class, [
                'label' => 'Tipo cliente:',
//                'placeholder' => '-Seleccione-',
                'choices' => [
                    'Persona natural' => 'individual_client',
                    'Cliente Empresarial' => 'enterprise_client',
                ],
                'mapped' => false,
                'expanded' => true,
                'multiple' => false,
//                'required' => false,
                'data' => 'individual_client',
                'attr' => [
                    'data-action' => 'change->visibility#toggle'//show or hide representative field
                ],
            ])
            //hacer la iteracion por los clientes
            ->add('client', HiddenType::class, [
//                'class' => Client::class,
//                'choice_label' => 'id',
            ])
            ->add('individualClient', EntityType::class, [
                'class' => IndividualClient::class,
                'choice_label' => 'id',
                'mapped' => false,
                'label' => 'Persona natural',
                'placeholder' => '-Seleccione-'
            ])
            ->add('enterpriseClient', EntityType::class, [
                'class' => EnterpriseClient::class,
                'choice_label' => 'representative',
                'mapped' => false,
                'label' => 'Cliente empresarial',
                'placeholder' => '-Seleccione-'
            ])
            ->add('investment', EntityPlusType::class, [
                'class' => Investment::class,
                'choice_label' => 'name',
                'label' => 'Inversión:',
                'placeholder' => '-Seleccione-',
                'modal_id' => '#add-investment',
                'detail' => true,
                'detail_title' => 'Detalle de la Inversión',
                'detail_id' => 'detail_investment_entity',
                'detail_loading' => 'Cargando detalles de la inversión...',
                'detail_url' => $this->router->generate('app_investment_show', ['id' => 0, 'state' => 'modal']),
                'query_builder' => $this->getInvestmentQueryBuilder($options),
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Project::class,
            'attr' => [
                'novalidate' => 'novalidate'
            ]
        ]);
    }

    /**
     * @param array $options
     * @return Closure
     */
    private function getInvestmentQueryBuilder(array $options): \Closure
    {
        return function (EntityRepository $er) use ($options): QueryBuilder|array {
            return $er->createQueryBuilder('i')
                ->join('i.project', 'p')
//                ->where('i.project is null')
                ->orderBy('i.name');
        };
    }
}
