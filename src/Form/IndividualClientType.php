<?php

namespace App\Form;

use App\Entity\Client;
use App\Entity\IndividualClient;
use App\Entity\Person;
use App\Entity\Representative;
use App\Form\Types\EntityPlusType;
use App\Form\Types\StreetAddressType;
use Closure;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

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
            'placeholder' => '-Seleccione-',
            'label' => 'Representante:',
//            'query_builder' => $this->getPersonQueryBuilder($ec),
            'modal_id' => '#add-person',
//            'path' => $this->router->generate('app_person_options', ['id' => 0]),
            'path' => '',//esta en un form en un live-compoentn
            'detail' => true,
            'detail_title' => 'Detalle de los representantes',
            'detail_id' => 'detail_person',
            'detail_loading' => 'Cargando detalles de los representantes...',
            'detail_url' => $this->router->generate('app_person_show', ['id' => 0, 'state' => 'modal']),
        ]);

        $form->add('hasRepresentative', CheckboxType::class, [
            'label' => 'Tiene representante',
            'mapped' => false,
            'required' => false,
            'attr' => [
                'data-action' => 'change->visibility#toggle'//show or hide representative field
            ],
            'data' => (bool)$ic->getRepresentative()
        ]);
    }
}
