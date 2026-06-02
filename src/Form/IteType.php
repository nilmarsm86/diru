<?php

namespace App\Form;

use App\Entity\Ite;
use App\Entity\IteProjectType;
use App\Entity\IteSource;
use App\Form\Types\CityCountryType;
use App\Form\Types\EntityPlusType;
use App\Form\Types\IteQualityEnumType;
use App\Form\Types\MeasurementUnitEntityPlusType;
use App\Form\Types\TrixEditorType;
use App\Form\Types\UnitMeasurementFloatType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

/**
 * @template TData of Ite
 *
 * @extends AbstractType<Ite>
 */
class IteType extends AbstractType
{
    public function __construct(
        private readonly RouterInterface $router,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $yearList = range((int) date('Y') - 10, (int) date('Y'));

        $builder
//            ->add('type')

            ->add('min', UnitMeasurementFloatType::class, [
                'label' => 'Mínimo:',
                'unit' => 'm<sup>2</sup>',
                'attr' => [
                    'min' => 0,
                ],
            ])
            ->add('max', UnitMeasurementFloatType::class, [
                'label' => 'Máximo:',
                'unit' => 'm<sup>2</sup>',
                'attr' => [
                    'min' => 0,
                ],
            ])
            ->add('yearReference', ChoiceType::class, [
                'label' => 'Año de referencia:',
                'placeholder' => '-Seleccione-',
                'choices' => array_combine($yearList, $yearList),
                'data' => date('Y'),
            ])
            ->add('comment', TrixEditorType::class, [
                'label' => 'Comentario:',
                'attr' => [
                    'placeholder' => 'Comentario de ayuda',
                    'hidden' => null,
                ],
                'required' => false,
            ])
            ->add('sourceAccess', null, [
                'label' => 'Fuente de acceso:',
            ])
            ->add('measurementUnit', MeasurementUnitEntityPlusType::class)
            ->add('source', EntityPlusType::class, [
                'class' => IteSource::class,
                'choice_label' => 'name',
                'label' => 'Fuente de información',
                'add' => true,
                'add_title' => 'Agregar fuente de información',
                'add_id' => 'modal-load',
                'add_url' => $this->router->generate('app_ite_source_new', ['modal' => 'modal-load']),
            ])

            ->add('cityCountry', CityCountryType::class, [
                'country' => $options['country'],
                'city' => $options['city'],
                'mapped' => false,
                'live_form' => $options['live_form'],
            ])
            ->add('projectType', EntityPlusType::class, [
                'class' => IteProjectType::class,
                'choice_label' => 'name',
                'label' => 'Tipo de proyecto',
                'add' => true,
                'add_title' => 'Agregar tipo de proyecto',
                'add_id' => 'modal-load',
                'add_url' => $this->router->generate('app_ite_project_type_new', ['modal' => 'modal-load']),
            ])
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void {
            $this->onPreSetData($event);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ite::class,
            'attr' => [
                'novalidate' => 'novalidate',
            ],
            'country' => 0,
            'city' => 0,
            'error_mapping' => [
                'enumQuality' => 'quality',
            ],
            'live_form' => false,
            'modal' => null,
        ]);

        $resolver->setAllowedTypes('country', 'int');
        $resolver->setAllowedTypes('city', 'int');
        $resolver->setAllowedTypes('live_form', 'bool');
        $resolver->setAllowedTypes('modal', ['null', 'string']);
    }

    /** @SuppressWarnings(PHPMD.UnusedFormalParameter) */
    private function onPreSetData(FormEvent $event): void
    {
        /** @var Ite $ite */
        $ite = $event->getData();
        $form = $event->getForm();

        if (null !== $ite && null !== $ite->getId()) {
        }

        $form->add('quality', IteQualityEnumType::class, [
            'label' => 'Calidad:',
        ]);
    }
}
