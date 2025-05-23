<?php

namespace App\Form;

use App\Entity\Constructor;
use App\Entity\Investment;
use App\Entity\LocationZone;
use App\Entity\Municipality;
use App\Form\Types\EntityPlusType;
use App\Form\Types\StreetAddressType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InvestmentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'label' => 'Nombre de la inversión:',
                'attr' => [
                    'placeholder' => 'Nombre de la inversión'
                ]
            ])
            ->add('locationZone', EntityPlusType::class, [
                'class' => LocationZone::class,
                'choice_label' => 'name',
                'required' => false,
                'label' => 'Zona de ubicación:',
                'modal_id' => '#add-location-zone',
                'path' => ''
            ])
            ->add('betweenStreets', null, [
                'label' => 'Entre calles:',
                'attr' => [
                    'placeholder' => 'Entre calles'
                ]
            ])
            ->add('town', null, [
                'label' => 'Reparto/Pueblo:',
                'attr' => [
                    'placeholder' => 'Nombre del reparto o pueblo'
                ]
            ])
            ->add('popularCouncil', null, [
                'label' => 'Consejo popular:',
                'attr' => [
                    'placeholder' => 'Consejo popular'
                ]
            ])
            ->add('block', null, [
                'label' => 'Manzana:',
                'attr' => [
                    'placeholder' => 'Manzana'
                ]
            ])
            ->add('district', null, [
                'label' => 'Circunscripción:',
                'attr' => [
                    'placeholder' => 'Circunscripción'
                ]
            ])
            ->add('addressNumber', null, [
                'label' => 'Número:',
                'attr' => [
                    'placeholder' => 'Número'
                ]
            ])
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
