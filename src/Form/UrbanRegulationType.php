<?php

namespace App\Form;

use App\Entity\Project;
use App\Entity\UrbanRegulation;
use App\Entity\UrbanRegulationType as Type;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UrbanRegulationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('code', null, [
                'label' => 'Código:',
                'attr' => [
                    'placeholder' => 'Código de la regulación'
                ]
            ])
            ->add('description', null, [
                'label' => 'Descripción:',
                'attr' => [
                    'placeholder' => 'Descripción de la regulación'
                ]
            ])
            ->add('data', null, [
                'label' => 'Dato:',
                'attr' => [
                    'placeholder' => 'Dato de la regulación'
                ]
            ])
            ->add('measurementUnit', null, [
                'label' => 'Unidad de medida:',
                'attr' => [
                    'placeholder' => 'Unidad de medida del dato'
                ]
            ])
            ->add('photo', FileType::class, [
                'label' => "Foto:",
                'required' => false,
            ])
            ->add('comment', null, [
                'label' => 'Comentario:',
                'attr' => [
                    'placeholder' => 'Comentario de ayuda'
                ]
            ])
            ->add('legalReference', null, [
                'label' => 'Referencia legal',
                'attr' => [
                    'placeholder' => 'Referencia legal que sustenta la regulación'
                ]
            ])
            ->add('type', EntityType::class, [
                'class' => Type::class,
                'choice_label' => 'name',
            ])
//            ->add('projects', EntityType::class, [
//                'class' => Project::class,
//                'choice_label' => 'id',
//                'multiple' => true,
//            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UrbanRegulation::class,
        ]);
    }
}
