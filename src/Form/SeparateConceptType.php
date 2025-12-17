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
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', SeparateConceptTypeEnumType::class, [
                'label' => 'Tipo:',
            ])
            ->add('number', NumberType::class, [
                'html5' => true,
                'label' => 'Número:',
            ])
            ->add('formula')
            ->add('name')
            ->add('parent', EntityType::class, [
                'class' => SeparateConcept::class,
                'choice_label' => 'number',
                'label' => 'Concepto padre:',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SeparateConcept::class,
        ]);
    }
}
