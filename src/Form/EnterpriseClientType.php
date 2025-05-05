<?php

namespace App\Form;

use App\Entity\CorporateEntity;
use App\Entity\EnterpriseClient;
use App\Entity\Municipality;
use App\Entity\Person;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EnterpriseClientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('phone')
            ->add('email')
            ->add('street')
            ->add('person', EntityType::class, [
                'class' => Person::class,
                'choice_label' => 'id',
            ])
            ->add('municipality', EntityType::class, [
                'class' => Municipality::class,
                'choice_label' => 'id',
            ])
            ->add('corporateEntity', EntityType::class, [
                'class' => CorporateEntity::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EnterpriseClient::class,
        ]);
    }
}
