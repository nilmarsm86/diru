<?php

namespace App\Form;

use App\Entity\CorporateEntity;
use App\Entity\EnterpriseClient;
use App\Entity\Municipality;
use App\Entity\Person;
use App\Form\Types\EntityPlusType;
use App\Form\Types\StreetAddressType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EnterpriseClientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('phone', null, [
                'label' => 'Teléfono:'
            ])
            ->add('email', EmailType::class, [
                'label' => 'Correo:'
            ])
            ->add('person', PersonType::class)
            ->add('streetAddress', StreetAddressType::class, [
                'street' => $options['street'],
                'province' => $options['province'],
                'municipality' => $options['municipality'],
                'mapped' => false,
                'live_form' => $options['live_form']
            ])
            ->add('corporateEntity', EntityPlusType::class, [
                'class' => CorporateEntity::class,
                'placeholder' => $options['province'] ? null : '-Seleccione-',
                'label' => 'Entidades:',
//                'mapped' => false,
//                'constraints' => $this->getProvinceConstraints($options),
//                'data' => $province,
//                'query_builder' => $this->getProvinceQueryBuilder($options),
                'modal_id' => '#add-entity',
                'path' => 'app_corporate_entity_options'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EnterpriseClient::class,
            'attr' => [
                'novalidate' => 'novalidate'
            ],
            'province' => 0,
            'municipality' => 0,
            'street' => '',
            'live_form' => false
        ]);
    }
}
