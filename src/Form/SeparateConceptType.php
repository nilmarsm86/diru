<?php

namespace App\Form;

use App\Entity\SeparateConcept;
use App\Repository\SeparateConceptRepository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfonycasts\DynamicForms\DependentField;
use Symfonycasts\DynamicForms\DynamicFormBuilder;

/**
 * @template TData of SeparateConcept
 *
 * @extends AbstractType<SeparateConcept>
 */
class SeparateConceptType extends AbstractType
{
    public function __construct(private SeparateConceptRepository $separateConceptRepository)
    {
    }

    /**
     * @param FormBuilderInterface<SeparateConcept|null> $builder
     * @param array<string, mixed>                       $options
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder = new DynamicFormBuilder($builder);

        $builder
            ->add('number', NumberType::class, [
                'html5' => true,
                'label' => 'Número:',
            ])
            ->add('formula', null, [
                'label' => 'Fórmula:',
            ])
            ->add('name', null, [
                'label' => 'Nombre:',
            ])
            ->add('parent', EntityType::class, [
                'placeholder' => 'Seleccione',
                'class' => SeparateConcept::class,
                'choice_label' => function (SeparateConcept $separateConcept) {
                    return $separateConcept->getNumber().' - '.$separateConcept->getName();
                },
                'label' => 'Concepto padre:',
                'required' => false,
                'query_builder' => $this->getParentConcepts(),
            ]);

        $builder->addDependent('childs', 'parent', function (DependentField $field, ?SeparateConcept $separateConcept) use ($options) {
            $isValid = (!is_null($separateConcept));
            if ((bool) $options['hasParent']) {
            }
            $typeAttr = [
                'class' => SeparateConcept::class,
                'placeholder' => $isValid ? '-Seleccione-' : '-Seleccione un concepto padre-',
                'label' => 'Conceptos hijos:',
                'mapped' => false,
                //                'constraints' => $this->getTypeConstraints($options),
                //                'query_builder' => $this->getChildsConcepts($separateConcept),
                'attr' => ['disabled' => !$isValid],
                'choice_label' => function (SeparateConcept $separateConcept) {
                    return $separateConcept->getNumber().' - '.$separateConcept->getName();
                },
                'required' => false,
                //                'choices' => $this->getChildsConcepts($separateConcept),
                'choices' => $this->separateConceptRepository->findSubtree($separateConcept?->getId() ?? 0),
            ];

            //            if (0 !== $options['type']) {
            //                $type = $this->subsystemTypeRepository->find($options['type']);
            //                $typeAttr['data'] = $type;
            //            }

            $field->add(EntityType::class, [] + $typeAttr);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SeparateConcept::class,
            'attr' => [
                'novalidate' => 'novalidate',
            ],
            'hasParent' => false,
        ]);

        $resolver->setAllowedTypes('hasParent', ['bool', 'null']);
    }

    private function getParentConcepts(): \Closure
    {
        return fn (EntityRepository $er): QueryBuilder => $er->createQueryBuilder('sc')
            ->where('sc.parent IS NULL')
            ->orderBy('sc.number', 'ASC');
    }

    //    private function getChildsConcepts(?SeparateConcept $parentSeparateConcept = null): array
    //    {
    //        return $this->separateConceptRepository->findSubtree($parentSeparateConcept?->getId() ?? 0);
    //    }
}
