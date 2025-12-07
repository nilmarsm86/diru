<?php

namespace App\Form;

use App\Entity\EnterpriseClient;
use App\Entity\IndividualClient;
use App\Entity\Project;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuickProjectType extends AbstractType
{
//    public function __construct(private readonly RouterInterface $router)
//    {
//
//    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'label' => 'Nombre:',
            ])
            //solo se muestra si el estado que se selecciona es el del parado
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
//                'choice_label' => 'representative',
                'choice_label' => function (EnterpriseClient $enterpriseClient) {
                    return $enterpriseClient->getEmail();
                },
                'group_by' => fn(EnterpriseClient $enterpriseClient, int $key, string $value) => $enterpriseClient->getRepresentative(),
                'mapped' => false,
                'label' => 'Cliente empresarial-negocio',
//                'placeholder' => '-Seleccione-'
            ])
            ->add('button', HiddenType::class, [
                'mapped' => false
            ])
//            ->add('moreData', SubmitType::class, [
//                'label' => 'Llenar mas datos',
//            ])
//            ->add('landData', SubmitType::class, [
//                'label' => 'Datos del terreno'
//            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Project::class,
            'attr' => [
                'novalidate' => 'novalidate',
                'id' => 'quick_project'
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
//    private function getInvestmentQueryBuilder(array $options): \Closure
//    {
//        return function (EntityRepository $er) use ($options): QueryBuilder|array {
//            return $er->createQueryBuilder('i')
//                ->join('i.project', 'p')
////                ->where('i.project is null')
//                ->orderBy('i.name');
//        };
//    }
}
