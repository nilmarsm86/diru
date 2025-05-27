<?php

namespace App\Form;

use App\Entity\Client;
use App\Entity\Investment;
use App\Entity\Person;
use App\Entity\Project;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('type')
            ->add('state')
            ->add('stopReason')
            ->add('hasOccupiedArea')
            ->add('registerAt', null, [
                'widget' => 'single_text',
            ])
            ->add('stoppedAt', null, [
                'widget' => 'single_text',
            ])
            ->add('canceledAt', null, [
                'widget' => 'single_text',
            ])
            ->add('initiatedAt', null, [
                'widget' => 'single_text',
            ])
            ->add('completedDiagnosticStatusAt', null, [
                'widget' => 'single_text',
            ])
            ->add('urbanRregulationAt', null, [
                'widget' => 'single_text',
            ])
            ->add('designAt', null, [
                'widget' => 'single_text',
            ])
            ->add('comment')
            ->add('draftsmans', EntityType::class, [
                'class' => Person::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])
            ->add('client', EntityType::class, [
                'class' => Client::class,
                'choice_label' => 'id',
            ])
            ->add('investment', EntityType::class, [
                'class' => Investment::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Project::class,
            'attr' => [
                'novalidate' => 'novalidate'
            ]
        ]);
    }
}
