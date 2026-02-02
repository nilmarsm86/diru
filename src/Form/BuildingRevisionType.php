<?php

namespace App\Form;

use App\Entity\BuildingRevision;
use App\Form\Types\TrixEditorType;
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
            ->add('comment', TrixEditorType::class, [
                'label' => 'Contenido',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BuildingRevision::class,
            'attr' => [
                'novalidate' => 'novalidate',
            ],
        ]);
    }
}
