<?php

namespace App\Form;

use App\Entity\Contract;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContractType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('code', null, [
                'label' => 'Código:',
                'attr' => [
                    'placeholder' => 'Código del contrato'
                ]
            ])
            //ponerlo como un select
            ->add('year', null, [
                'label' => 'Año:',
                'attr' => [
                    'placeholder' => 'Año del contrato',
                    'min' => ((int)date('Y') - 5),
                    'max' => ((int)date('Y') + 5),
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contract::class,
            'attr' => [
                'novalidate' => 'novalidate'
            ]
        ]);
    }
}
