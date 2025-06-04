<?php

namespace App\Form\Types;

use App\Entity\Municipality;
use App\Entity\Province;
use App\Repository\MunicipalityRepository;
use App\Repository\ProvinceRepository;
use Closure;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class AddressType extends AbstractType
{
    public function __construct(
        private readonly ProvinceRepository     $provinceRepository,
        private readonly MunicipalityRepository $municipalityRepository,
        private readonly RouterInterface        $router
    )
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $province = $this->provinceRepository->find($options['province']);
        $municipality = $this->municipalityRepository->find($options['municipality']);

        $provinceAttr = [
            'class' => Province::class,
            'placeholder' => $options['province'] ? null : '-Seleccione-',
            'label' => 'Provincia:',
            'mapped' => false,
            'constraints' => $this->getProvinceConstraints($options),
//            'attr' => [
//                'data-model' => 'province'
//            ],
            'data' => $province,
            'query_builder' => $this->getProvinceQueryBuilder($options),
        ];

        if (is_null($options['modal'])) {
            $builder->add('province', EntityPlusType::class, [
//                'modal_id' => '#add-province',
//                'path' => '',//como el formulario es live-component cuando se agregar el dato se recarga y trae el dato nuevo por eso se puede dejar vacio
                    'add' => true,
                    'add_title' => 'Agregar Provincia',
                    'add_id' => 'modal-load',
                    'add_url' => $this->router->generate('app_province_new', ['modal' => 'modal-load']),
                ] + $provinceAttr);
        } else {
            $builder->add('province', EntityType::class, [] + $provinceAttr);
        }

        $municipalityAttr = [
            'class' => Municipality::class,
            'placeholder' => $options['municipality'] ? null : '-Seleccione una provincia-',
            'label' => 'Municipio:',
//            'mapped' => false,
            'constraints' => $this->getMunicipalityConstraints($options),
//            'attr' => [
//                'data-model' => 'province'
//            ],
            'data' => $municipality,
            'query_builder' => $this->getMunicipalityQueryBuilder($options),
        ];

        if (is_null($options['modal'])) {
            $builder->add('municipality', EntityPlusType::class, [
//                    'modal_id' => '#add-municipality',
//                    'path' => ''//si esta vacio es que esta en un live-component-form que se recarga al agregar el nuevo elemento
                    'add' => true,
                    'add_title' => 'Agregar Municipio',
                    'add_id' => 'modal-load',
                    'add_url' => $this->router->generate('app_municipality_new', ['modal' => 'modal-load']),
                ] + $municipalityAttr);
        } else {
            $builder->add('municipality', EntityType::class, [] + $municipalityAttr);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'province' => 0,
            'municipality' => 0,
            'row' => false,
            'col' => true,
            'live_form' => false,
            'modal' => null
        ]);

        $resolver->setAllowedTypes('province', ['int']);
        $resolver->setAllowedTypes('municipality', ['int']);
        $resolver->setAllowedTypes('row', 'bool');
        $resolver->setAllowedTypes('col', 'bool');
        $resolver->setAllowedTypes('live_form', 'bool');
        $resolver->setAllowedTypes('modal', ['null', 'string']);
    }

    /**
     * {@inheritDoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['row'] = $options['row'];
        $view->vars['col'] = $options['col'];
        $view->vars['live_form'] = $options['live_form'];
    }

    /**
     * @param array $options
     * @return array|NotBlank[]
     */
    private function getMunicipalityConstraints(array $options): array
    {
        $municipalityConstraints = [];
        if ($options['municipality'] === 0) {
            $municipalityConstraints = [
                new NotBlank(message: 'Seleccione un municipio.')
            ];
        }
//        else {
//            if ($options['province'] !== 0) {
//                $municipalityConstraints = [
//                    new NotBlank(message: 'Seleccione un municipio.')
//                ];
//            }
//        }

        return $municipalityConstraints;
    }

    /**
     * @param array $options
     * @return array|NotBlank[]
     */
    private function getProvinceConstraints(array $options): array
    {
        $provinceConstraints = [];
        if ($options['province'] === 0) {
            $provinceConstraints = [
                new NotBlank(message: 'Seleccione una provincia.')
            ];
        }

        return $provinceConstraints;
    }

    /**
     * @param array $options
     * @return Closure
     */
    private function getProvinceQueryBuilder(array $options): Closure
    {
        return function (EntityRepository $er) use ($options): QueryBuilder|array {
            return $er->createQueryBuilder('p')->orderBy('p.name');
        };
    }

    /**
     * @param array $options
     * @return Closure
     */
    private function getMunicipalityQueryBuilder(array $options): Closure
    {
//        if ($options['municipality']) {
//            return function (EntityRepository $er) use ($options): QueryBuilder|array {
//                return $er->createQueryBuilder('m')->where('m.id = ' . $options['municipality']);
//            };
//        } else {
        return function (EntityRepository $er) use ($options): QueryBuilder|array {
            return $er->createQueryBuilder('m')->where('m.province = ' . $options['province']);
        };
//        }
    }

}
