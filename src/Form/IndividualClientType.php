<?php

namespace App\Form;

use App\Entity\IndividualClient;
use App\Entity\Representative;
use App\Form\Types\EntityPlusType;
use App\Form\Types\StreetAddressType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

/**
 * @template TData of IndividualClient
 *
 * @extends AbstractType<IndividualClient>
 */
class IndividualClientType extends AbstractType
{
    public function __construct(private readonly RouterInterface $router)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('person', PersonType::class)
            ->add('phone', null, [
                'label' => 'Teléfono:',
                'attr' => [
                    'placeholder' => 'Teléfono del cliente',
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Correo:',
                'attr' => [
                    'placeholder' => 'Correo del cliente',
                ],
            ])
            ->add('streetAddress', StreetAddressType::class, [
                'street' => $options['street'],
                'province' => $options['province'],
                'municipality' => $options['municipality'],
                'mapped' => false,
                'live_form' => $options['live_form'],
            ])
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void {
            $this->onPreSetData($event);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => IndividualClient::class,
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
        $resolver->setAllowedTypes('street', 'string');
        $resolver->setAllowedTypes('live_form', 'bool');
        $resolver->setAllowedTypes('modal', ['null', 'string']);
    }

    private function onPreSetData(FormEvent $event): void
    {
        $form = $event->getForm();

        $form->add('representative', EntityPlusType::class, [
            'class' => Representative::class,
            'required' => false,
            'label' => false,
            'detail' => true,
            'detail_title' => 'Detalle del representante',
            'detail_id' => 'modal-load',
            'detail_url' => $this->router->generate('app_representative_show', ['id' => 0, 'state' => 'modal']),

            'add' => true,
            'add_title' => 'Agregar representante',
            'add_id' => 'modal-load',
            'add_url' => $this->router->generate('app_representative_new', ['modal' => 'modal-load']),
        ]);
    }
}
