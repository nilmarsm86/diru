<?php

namespace App\Form;

use App\Entity\ProjectUrbanRegulation;
use App\Entity\UrbanRegulation;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @template TData of ProjectUrbanRegulation
 *
 * @extends AbstractType<ProjectUrbanRegulation>
 */
class ProjectUrbanRegulationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('urbanRegulationType', EntityType::class, [
                'class' => \App\Entity\UrbanRegulationType::class,
                'choice_label' => 'name',
                'mapped' => false,
                'label' => 'Tipo de regulación:',
            ])
            ->add('urbanRegulation', EntityType::class, [
                'class' => UrbanRegulation::class,
                'choice_label' => 'description',
                'label' => 'Regulación:',
            ])
            ->add('data', null, [
                'label' => 'Dato:',
            ])
            ->add('reference', null, [
                'label' => 'Referencia:',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProjectUrbanRegulation::class,
        ]);
    }
}
