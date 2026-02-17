<?php

namespace App\Form\Types;

use App\Entity\Enums\SubsystemFunctionalClassification;
use App\Entity\SubsystemSubType;
use App\Entity\SubsystemType;
use App\Repository\SubsystemSubTypeRepository;
use App\Repository\SubsystemTypeRepository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfonycasts\DynamicForms\DependentField;
use Symfonycasts\DynamicForms\DynamicFormBuilder;

/**
 * @template TData of array
 *
 * @extends AbstractType<array>
 */
class SubSystemClassificationType extends AbstractType
{
    public function __construct(
        private readonly SubsystemTypeRepository $subsystemTypeRepository,
        private readonly SubsystemSubTypeRepository $subsystemSubTypeRepository,
        private readonly RouterInterface $router
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder = new DynamicFormBuilder($builder);

        $classification = [];
        if (0 !== $options['type']) {
            $type = $this->subsystemTypeRepository->find($options['type']);
            $classification['data'] = $type?->getClassification();
        }

        $builder->add('classification', SubsystemFunctionalClassificationEnumType::class, [
            'label' => 'Clasificación:',
            'mapped' => false,
        ] + $classification);

        $builder->addDependent('type', 'classification', function (DependentField $field, ?SubsystemFunctionalClassification $subsystemFunctionalClassification) use ($options) {
            $isValid = (!is_null($subsystemFunctionalClassification) && ('' !== $subsystemFunctionalClassification->value));
            $typeAttr = [
                'class' => SubsystemType::class,
                'placeholder' => $isValid ? '-Seleccione-' : '-Seleccione una clasificación-',
                'label' => 'Tipo:',
                'mapped' => false,
                'constraints' => $this->getTypeConstraints($options),
                'query_builder' => $this->getTypeQueryBuilder($subsystemFunctionalClassification),
                'attr' => ['disabled' => !$isValid],

                'add' => true,
                'add_title' => 'Agregar Tipo',
                'add_id' => 'modal-load',
                'add_url' => $this->router->generate('app_subsystem_type_new', ['modal' => 'modal-load'])
            ];

            if (0 !== $options['type']) {
                $type = $this->subsystemTypeRepository->find($options['type']);
                $typeAttr['data'] = $type;
            }

            $field->add(EntityPlusType::class, [] + $typeAttr);
        });

        $builder->addDependent('subType', 'type', function (DependentField $field, ?SubsystemType $subsystemType) use ($options) {
            $subType = $this->subsystemSubTypeRepository->find($options['subType']);
            $subTypeAttr = [
                'class' => SubsystemSubType::class,
                'placeholder' => !is_null($subsystemType) ? '-Seleccione-' : '-Seleccione un tipo-',
                'label' => 'Sub tipo:',
                'constraints' => $this->getSubTypeConstraints($subsystemType),
                'data' => $subType,
                'query_builder' => $this->getSubTypeQueryBuilder($subsystemType),
                'attr' => ['disabled' => is_null($subsystemType)],

                'add' => true,
                'add_title' => 'Agregar Subtipo',
                'add_id' => 'modal-load',
                'add_url' => $this->router->generate('app_subsystem_sub_type_new', ['modal' => 'modal-load'])
            ];

            $field->add(EntityPlusType::class, $subTypeAttr);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'type' => 0,
            'subType' => 0,
            'row' => false,
            'col' => true,
            'live_form' => false,
            'modal' => null,
        ]);

        $resolver->setAllowedTypes('type', ['int']);
        $resolver->setAllowedTypes('subType', ['int']);
        $resolver->setAllowedTypes('row', 'bool');
        $resolver->setAllowedTypes('col', 'bool');
        $resolver->setAllowedTypes('live_form', 'bool');
        $resolver->setAllowedTypes('modal', ['null', 'string']);
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['row'] = $options['row'];
        $view->vars['col'] = $options['col'];
        $view->vars['live_form'] = $options['live_form'];
    }

    /**
     * @return array<mixed>|NotBlank[]
     */
    private function getSubTypeConstraints(?SubsystemType $subsystemType): array
    {
        $subTypeConstraints = [];
        if (is_null($subsystemType)) {
            $subTypeConstraints = [
                new NotBlank(message: 'Seleccione un sub tipo.'),
            ];
        }

        return $subTypeConstraints;
    }

    /**
     * @param array<mixed> $options
     *
     * @return array<mixed>|NotBlank[]
     */
    private function getTypeConstraints(array $options): array
    {
        $typeConstraints = [];
        if (0 === $options['type']) {
            $typeConstraints = [
                new NotBlank(message: 'Seleccione un tipo.'),
            ];
        }

        return $typeConstraints;
    }

    private function getTypeQueryBuilder(?SubsystemFunctionalClassification $subsystemFunctionalClassification): \Closure
    {
        return fn (SubsystemTypeRepository $subsystemTypeRepository): QueryBuilder => $subsystemTypeRepository->findSubsystemTypeForForm($subsystemFunctionalClassification);
    }

    private function getSubTypeQueryBuilder(?SubsystemType $subsystemType = null): \Closure
    {
        return fn (EntityRepository $er): QueryBuilder => $er->createQueryBuilder('ssst')
            ->leftJoin('ssst.subsystemTypeSubsystemSubTypes', 'sstssst')
            ->leftJoin('sstssst.subsystemType', 'sst')
            ->where('sst.id = :sst_id')
            ->setParameter('sst_id', !is_null($subsystemType) ? $subsystemType->getId() : 0);
    }
}
