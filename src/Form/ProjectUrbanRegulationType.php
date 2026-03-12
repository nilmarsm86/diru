<?php

namespace App\Form;

use App\Entity\ProjectUrbanRegulation;
use App\Entity\UrbanRegulation;
use App\Form\Types\TrixEditorType;
use App\Repository\UrbanRegulationRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfonycasts\DynamicForms\DependentField;
use Symfonycasts\DynamicForms\DynamicFormBuilder;

/**
 * @template TData of ProjectUrbanRegulation
 *
 * @extends AbstractType<ProjectUrbanRegulation>
 */
class ProjectUrbanRegulationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder = new DynamicFormBuilder($builder);

        $builder
            ->add('urbanRegulationType', EntityType::class, [
                'class' => \App\Entity\UrbanRegulationType::class,
                'choice_label' => 'name',
                'mapped' => false,
                'label' => 'Tipo de regulación:',
                'placeholder' => '-Seleccione-',
            ]);
        $builder->addDependent('urbanRegulation', 'urbanRegulationType', function (DependentField $field, ?\App\Entity\UrbanRegulationType $urbanRegulationType) {
            $isValid = !is_null($urbanRegulationType);
            $field->add(EntityType::class, [
                'class' => UrbanRegulation::class,
                'choice_label' => 'description',
                'label' => 'Regulación:',
                'placeholder' => $isValid ? '-Seleccione-' : '-Seleccione un tipo de regulación-',
                'query_builder' => $this->getTypeQueryBuilder($urbanRegulationType),
                'attr' => ['disabled' => !$isValid],
            ]);
        });

        $builder->add('data', null, [
            'label' => 'Dato:',
        ])
            ->add('reference', TrixEditorType::class, [
                'label' => 'Referencia:',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProjectUrbanRegulation::class,
            'attr' => [
                'novalidate' => 'novalidate',
            ],
        ]);
    }

    private function getTypeQueryBuilder(?\App\Entity\UrbanRegulationType $urbanRegulationType = null): \Closure
    {
        return fn (UrbanRegulationRepository $urbanRegulationRepository): QueryBuilder => $urbanRegulationRepository->findUrbanRegulationForForm($urbanRegulationType);
    }
}
