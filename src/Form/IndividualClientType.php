<?php

namespace App\Form;

use App\Entity\IndividualClient;
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
            'live_form' => false,
            'modal' => null
        ]);

        $resolver->setAllowedTypes('province', 'int');
        $resolver->setAllowedTypes('municipality', 'int');
        $resolver->setAllowedTypes('street', 'string');
        $resolver->setAllowedTypes('live_form', 'bool');
        $resolver->setAllowedTypes('modal', ['null', 'string']);
    }

//    /**
//     * @param Client $client
//     * @return Closure
//     */
//    private function getPersonQueryBuilder(Client $client): Closure
//    {
//        return function (EntityRepository $er) use ($client): QueryBuilder|array {
//            $qb = $er->createQueryBuilder('p');
//            if (!$client->getId()) {
//                $qb->leftJoin('p.client', 'c')
//                    ->where('c.person IS NULL');
//            } else {
//                $qb->leftJoin('p.client', 'c')
//                    ->where('c.person IS NULL')
//                    ->orWhere('c.id = :id')
//                    ->setParameter(':id', $client->getId());
//            }
//
//            return $qb->orderBy('p.name', 'ASC');
//        };
//    }

    /**
     * @param FormEvent $event
     * @return void
     */
    private function onPreSetData(FormEvent $event): void
    {
        /** @var IndividualClient $ic */
        $ic = $event->getData();
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

//        $form->add('hasRepresentative', CheckboxType::class, [
//            'label' => 'Tiene representante',
//            'mapped' => false,
//            'required' => false,
//            'attr' => [
//                'data-action' => 'change->visibility#toggle'//show or hide representative field
//            ],
//            'data' => (bool)$ic->getRepresentative()
//        ]);
    }
}
