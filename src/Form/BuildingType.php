<?php

namespace App\Form;

use App\Entity\Building;
use App\Entity\Constructor;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BuildingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('estimatedValueConstruction')
            ->add('estimatedValueEquipment')
            ->add('estimatedValueOther')
            ->add('approvedValueConstruction')
            ->add('approvedValueEquipment')
            ->add('approvedValueOther')
            ->add('name')
            ->add('constructor', EntityType::class, [
                'class' => Constructor::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Building::class,
        ]);
    }
}
