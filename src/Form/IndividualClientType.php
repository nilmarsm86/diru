<?php

namespace App\Form;

use App\Entity\Client;
use App\Entity\IndividualClient;
use App\Entity\Person;
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
use Symfony\Component\Validator\Constraints\NotBlank;

class IndividualClientType extends AbstractType
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
                    'placeholder' => 'Teléfono del cliente'
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => 'Correo:',
                'attr' => [
                    'placeholder' => 'Correo del cliente'
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

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void {
            $this->onPreSetData($event);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => IndividualClient::class,
            'attr' => [
                'novalidate' => 'novalidate'
            ],
            'province' => 0,
            'municipality' => 0,
            'street' => '',
            'live_form' => false
        ]);

        $resolver->setAllowedTypes('province', 'int');
        $resolver->setAllowedTypes('municipality', 'int');
        $resolver->setAllowedTypes('street', 'string');
        $resolver->setAllowedTypes('live_form', 'bool');
    }

    /**
     * @param Client $client
     * @return Closure
     */
    private function getPersonQueryBuilder(Client $client): Closure
    {
        return function (EntityRepository $er) use ($client): QueryBuilder|array {
            $qb = $er->createQueryBuilder('p');
            if (!$client->getId()) {
                $qb->leftJoin('p.client', 'c')
                    ->where('c.person IS NULL');
            } else {
                $qb->leftJoin('p.client', 'c')
                    ->where('c.person IS NULL')
                    ->orWhere('c.id = :id')
                    ->setParameter(':id', $client->getId());
            }

            return $qb->orderBy('p.name');
        };
    }

    /**
     * @param FormEvent $event
     * @return void
     */
    private function onPreSetData(FormEvent $event): void
    {
        $ec = $event->getData();
        $form = $event->getForm();

        $form->add('person', EntityPlusType::class, [
            'class' => Person::class,
            'placeholder' => '-Seleccione-',
            'label' => 'Representante:',
            'query_builder' => $this->getPersonQueryBuilder($ec),
            'modal_id' => '#add-person',
            'path' => $this->router->generate('app_person_options', ['id' => 0]),
            'detail' => true,
            'detail_title' => 'Detalle de los representantes',
            'detail_id' => 'detail_person',
            'detail_loading' => 'Cargando detalles de los representantes...',
            'detail_url' => $this->router->generate('app_person_show', ['id' => 0, 'state' => 'modal']),
            'constraints' => [new NotBlank(message: 'Seleccione o cree el representante.')],
        ]);
    }
}
