<?php

namespace App\Form;

use App\Entity\SeparateConcept;
use App\Form\Types\SeparateConceptTypeEnumType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @template TData of SeparateConcept
 *
 * @extends AbstractType<SeparateConcept>
 */
class SeparateConceptType extends AbstractType
{
    /**
     * @param FormBuilderInterface<SeparateConcept|null> $builder
     * @param array<string, mixed>                       $options
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
//            ->add('type', SeparateConceptTypeEnumType::class, [
//                'label' => 'Tipo:',
//            ])
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
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SeparateConcept::class,
            'attr' => [
                'novalidate' => 'novalidate',
            ],
            //            'error_mapping' => [
            //                'enumType' => 'type',
            //            ],
        ]);
    }
}
