<?php

namespace App\Form;

use App\Entity\Building;
use App\Entity\BuildingRevision;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @template TData of BuildingRevision
 *
 * @extends AbstractType<BuildingRevision>
 */
class BuildingRevisionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
//            ->add('createdAt', null, [
//                'widget' => 'single_text',
//            ])
            ->add('comment')
//            ->add('building', EntityType::class, [
//                'class' => Building::class,
//                'choice_label' => 'id',
//            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BuildingRevision::class,
        ]);
    }
}
