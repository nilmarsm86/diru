<?php

namespace App\Form;

use App\Entity\Constructor;
use App\Entity\Investment;
use App\Entity\LocationZone;
use App\Entity\Municipality;
use App\Form\Types\StreetAddressType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InvestmentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('workName', null, [
                'label' => 'Nombre de la obra:',
                'attr' => [
                    'placeholder' => 'Nombre de la obra'
                ]
            ])
            ->add('investmentName', null, [
                'label' => 'Nombre de la inversión:',
                'attr' => [
                    'placeholder' => 'Nombre de la inversión'
                ]
            ])
            ->add('hasSameName', CheckboxType::class, [
                'label' => 'Mismo nombre de inversión',
                'mapped' => false,
                'required' => false,
//                'attr' => [
//                    'data-action' => 'change->visibility#toggle'//show or hide representative field
//                ],
//                'data' => (bool)$ec->getRepresentative()
            ])
            ->add('constructor', EntityType::class, [
                'class' => Constructor::class,
                'choice_label' => 'name',
            ])
            ->add('estimatedValueConstruction')
            ->add('estimatedValueEquipment')
            ->add('estimatedValueOther')
            ->add('approvedValueConstruction')
            ->add('approvedValueEquipment')
            ->add('approvedValueOther')
            ->add('betweenStreets')
            ->add('town')
            ->add('popularCouncil')
            ->add('block')
            ->add('district')
//            ->add('street')
            ->add('addressNumber')

            ->add('locationZone', EntityType::class, [
                'class' => LocationZone::class,
                'choice_label' => 'id',
            ])
//            ->add('municipality', EntityType::class, [
//                'class' => Municipality::class,
//                'choice_label' => 'id',
//            ])
            ->add('streetAddress', StreetAddressType::class, [
                'street' => $options['street'],
                'province' => $options['province'],
                'municipality' => $options['municipality'],
                'mapped' => false,
                'live_form' => $options['live_form']
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Investment::class,
            'attr' => [
                'novalidate' => 'novalidate'
            ],
            'province' => 0,
            'municipality' => 0,
            'street' => '',
            'live_form' => false,
            'modal' => null
        ]);

        $resolver->setAllowedTypes('province', 'int');
        $resolver->setAllowedTypes('municipality', 'int');
        $resolver->setAllowedTypes('live_form', 'bool');
        $resolver->setAllowedTypes('modal', ['null', 'string']);
        $resolver->setAllowedTypes('street', 'string');
    }
}
