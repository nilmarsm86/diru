<?php

namespace App\Form;

use App\Entity\CorporateEntity;
use App\Entity\EnterpriseClient;
use App\Entity\Representative;
use App\Form\Types\EntityPlusType;
use App\Form\Types\StreetAddressType;
use Closure;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @template TData of EnterpriseClient
 *
 * @extends AbstractType<EnterpriseClient>
 */
class EnterpriseClientType extends AbstractType
{
    public function __construct(private readonly RouterInterface $router)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('phone', null, [
                'label' => 'Teléfono:',
                'attr' => [
                    'placeholder' => 'Teléfono de la empresa',
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Correo:',
                'attr' => [
                    'placeholder' => 'Correo de la empresa',
                ],
            ])
            ->add('streetAddress', StreetAddressType::class, [
                'street' => $options['street'],
                'province' => $options['province'],
                'municipality' => $options['municipality'],
                'mapped' => false,
                'live_form' => $options['live_form'],
            ])
            ->add('corporateEntity', EntityPlusType::class, [
                'class' => CorporateEntity::class,
                'label' => 'Entidad corporativa:',
                'query_builder' => $this->getEntityQueryBuilder(),

                'detail' => true,
                'detail_title' => 'Detalle de la entidad',
                'detail_id' => 'modal-load',
                'detail_url' => $this->router->generate('app_corporate_entity_show', ['id' => 0, 'state' => 'modal']),

                'add' => true,
                'add_title' => 'Agregar entidad',
                'add_id' => 'modal-load',
                'add_url' => $this->router->generate('app_corporate_entity_new', ['modal' => 'modal-load']),
            ])

        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void {
            $this->onPreSetData($event);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EnterpriseClient::class,
            'attr' => [
                'novalidate' => 'novalidate',
            ],
            'province' => 0,
            'municipality' => 0,
            'street' => '',
            'live_form' => false,
            'modal' => null,
        ]);

        $resolver->setAllowedTypes('province', 'int');
        $resolver->setAllowedTypes('municipality', 'int');
        $resolver->setAllowedTypes('live_form', 'bool');
        $resolver->setAllowedTypes('modal', ['null', 'string']);
        $resolver->setAllowedTypes('street', 'string');
    }

    private function getEntityQueryBuilder(): \Closure
    {
        return fn (EntityRepository $er): QueryBuilder => $er->createQueryBuilder('ce')
            ->andWhere('ce.type <> '.\App\Entity\Enums\CorporateEntityType::Constructor->value)
            ->orderBy('ce.name', 'ASC');
    }

    private function onPreSetData(FormEvent $event): void
    {
        $form = $event->getForm();

        $form->add('representative', EntityPlusType::class, [
            'class' => Representative::class,
            'label' => 'Representante:',
            'detail' => true,
            'detail_title' => 'Detalle del representante',
            'detail_id' => 'modal-load',
            'detail_url' => $this->router->generate('app_representative_show', ['id' => 0, 'state' => 'modal']),

            'add' => true,
            'add_title' => 'Agregar representante',
            'add_id' => 'modal-load',
            'add_url' => $this->router->generate('app_representative_new', ['modal' => 'modal-load']),

            'constraints' => [
                new Assert\NotBlank(message: 'Seleccione o cree un nuevo representante.'),
            ],
        ]);
    }
}
