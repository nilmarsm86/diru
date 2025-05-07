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
use Symfony\Component\Routing\RouterInterface;

class EnterpriseClientType extends AbstractType
{
    public function __construct(private readonly RouterInterface $router)
    {

    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('phone', null, [
                'label' => 'Teléfono:'
            ])
            ->add('email', EmailType::class, [
                'label' => 'Correo:'
            ])
//            ->add('person', PersonType::class)
            ->add('person', EntityPlusType::class, [
                'class' => Person::class,
                'placeholder' => '-Seleccione-',
                'label' => 'Representantes:',
//                'query_builder' => $this->getProvinceQueryBuilder($options),
                'modal_id' => '#add-person',
                'path' => $this->router->generate('app_person_options', ['id' => 0]),
                'detail' => true,
                'detail_title' => 'Detalle de los representantes',
                'detail_id' => 'detail_person',
                'detail_loading' => 'Cargando detalles de los representantes...',
                'detail_url' => $this->router->generate('app_person_show', ['id' => 0])
            ])
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
//                'query_builder' => $this->getProvinceQueryBuilder($options),
                'modal_id' => '#add-entity',
                'path' => $this->router->generate('app_corporate_entity_options', ['id' => 0]),
                'detail' => true,
                'detail_title' => 'Detalle de la entidad',
                'detail_id' => 'detail_corporate_entity',
                'detail_loading' => 'Cargando detalles de la entidad...',
                'detail_url' => $this->router->generate('app_corporate_entity_show', ['id' => 0])

            ]);
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
