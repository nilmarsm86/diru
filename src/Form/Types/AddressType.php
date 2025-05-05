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
use Symfony\Component\Validator\Constraints\NotBlank;

class AddressType extends AbstractType
{
    public function __construct(
        private readonly ProvinceRepository     $provinceRepository,
        private readonly MunicipalityRepository $municipalityRepository,
    )
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $province = $this->provinceRepository->find($options['province']);
        $municipality = $this->municipalityRepository->find($options['municipality']);

        $builder
            ->add('province', EntityPlusType::class, [
                'class' => Province::class,
                'placeholder' => $options['province'] ? null : '-Seleccione-',
                'label' => 'Provincia:',
                'mapped' => false,
                'constraints' => $this->getProvinceConstraints($options),
                'data' => $province,
                'query_builder' => $this->getProvinceQueryBuilder($options),
                'modal_id' => '#add-province',
                'path' => 'app_province_options'
            ])
            ->add('municipality', EntityPlusType::class, [
                'class' => Municipality::class,
                'placeholder' => $options['municipality'] ? null : '-Seleccione una provincia-',
                'query_builder' => $this->getMunicipalityQueryBuilder($options),
//                'disabled' => $options['province'] ? false : true,
                'label' => 'Municipio:',
                'constraints' => $this->getMunicipalityConstraints($options),
                'data' => $municipality,
                'modal_id' => '#add-municipality',
                'path' => 'app_municipality_options'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'province' => 0,
            'municipality' => 0,
            'row' => false,
            'col' => true,
            'live_form' => false
        ]);

        $resolver->setAllowedTypes('province', ['int']);
        $resolver->setAllowedTypes('municipality', ['int']);
        $resolver->setAllowedTypes('row', 'bool');
        $resolver->setAllowedTypes('col', 'bool');
        $resolver->setAllowedTypes('live_form', 'bool');
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
        if($options['municipality']){
            return function (EntityRepository $er) use ($options): QueryBuilder|array {
                return $er->createQueryBuilder('m')->where('m.id = '.$options['municipality']);
            };
        }else{
            return function (EntityRepository $er) use ($options): QueryBuilder|array {
                return $er->createQueryBuilder('m')->where('m.province = ' . $options['province']);
            };
        }
    }

}
