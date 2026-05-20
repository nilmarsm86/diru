<?php

namespace App\Form;

use App\Entity\City;
use App\Entity\Ite;
use App\Entity\IteProjectType;
use App\Entity\IteSource;
use App\Entity\MeasurementUnit;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @template TData of Ite
 *
 * @extends AbstractType<Ite>
 */
class IteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type')
            ->add('quality')
            ->add('min')
            ->add('max')
            ->add('yearReference')
            ->add('comment')
            ->add('sourceAccess')
            ->add('measurementUnit', EntityType::class, [
                'class' => MeasurementUnit::class,
                'choice_label' => 'id',
            ])
            ->add('source', EntityType::class, [
                'class' => IteSource::class,
                'choice_label' => 'id',
            ])
            ->add('city', EntityType::class, [
                'class' => City::class,
                'choice_label' => 'id',
            ])
            ->add('projectType', EntityType::class, [
                'class' => IteProjectType::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ite::class,
            'attr' => [
                'novalidate' => 'novalidate',
            ],
        ]);
    }
}
