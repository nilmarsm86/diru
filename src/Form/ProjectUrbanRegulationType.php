<?php

namespace App\Form;

use App\Entity\Project;
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
            ->add('data')
            ->add('urbanRegulation', EntityType::class, [
                'class' => UrbanRegulation::class,
                'choice_label' => 'id',
            ])
            ->add('project', EntityType::class, [
                'class' => Project::class,
                'choice_label' => 'id',
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
