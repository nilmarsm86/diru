<?php

namespace App\Form;

use App\Entity\SeparateConcept;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
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
            ->add('type')
            ->add('number')
            ->add('formula')
            ->add('name')
            ->add('parent', EntityType::class, [
                'class' => SeparateConcept::class,
                'choice_label' => 'id',
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
