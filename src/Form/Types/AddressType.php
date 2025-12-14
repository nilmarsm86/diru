<?php

namespace App\Form\Types;

use App\Entity\Municipality;
use App\Entity\Province;
use App\Repository\MunicipalityRepository;
use App\Repository\ProvinceRepository;
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

/**
 * @template TData of array
 *
 * @extends AbstractType<array>
 */
class AddressType extends AbstractType
{
    public function __construct(
        private readonly ProvinceRepository $provinceRepository,
        private readonly MunicipalityRepository $municipalityRepository,
        private readonly RouterInterface $router,
    ) {
    }

    /**
     * @param FormBuilderInterface<array|null> $builder
     * @param array<string, mixed>             $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $province = $this->provinceRepository->find($options['province']);
        $municipality = $this->municipalityRepository->find($options['municipality']);

        $provinceAttr = [
            'class' => Province::class,
            'placeholder' => (bool) $options['province'] ? null : '-Seleccione-',
            'label' => 'Provincia:',
            'mapped' => false,
            'constraints' => $this->getProvinceConstraints($options),
            'data' => $province,
            'query_builder' => $this->getProvinceQueryBuilder(),
        ];

        if (is_null($options['modal'])) {
            $builder->add('province', EntityPlusType::class, [
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
            'placeholder' => (bool) $options['municipality'] ? null : '-Seleccione una provincia-',
            'label' => 'Municipio:',
            'constraints' => $this->getMunicipalityConstraints($options),
            'data' => $municipality,
            'query_builder' => $this->getMunicipalityQueryBuilder($options),
        ];

        if (is_null($options['modal'])) {
            $builder->add('municipality', EntityPlusType::class, [
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
            'modal' => null,
        ]);

        $resolver->setAllowedTypes('province', ['int']);
        $resolver->setAllowedTypes('municipality', ['int']);
        $resolver->setAllowedTypes('row', 'bool');
        $resolver->setAllowedTypes('col', 'bool');
        $resolver->setAllowedTypes('live_form', 'bool');
        $resolver->setAllowedTypes('modal', ['null', 'string']);
    }

    /**
     * @param FormInterface<array> $form
     * @param array<string, mixed> $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['row'] = $options['row'];
        $view->vars['col'] = $options['col'];
        $view->vars['live_form'] = $options['live_form'];
    }

    /**
     * @param array<mixed> $options
     *
     * @return array<mixed>|NotBlank[]
     */
    private function getMunicipalityConstraints(array $options): array
    {
        $municipalityConstraints = [];
        if (0 === $options['municipality']) {
            $municipalityConstraints = [
                new NotBlank(message: 'Seleccione un municipio.'),
            ];
        }

        return $municipalityConstraints;
    }

    /**
     * @param array<mixed> $options
     *
     * @return array<mixed>|NotBlank[]
     */
    private function getProvinceConstraints(array $options): array
    {
        $provinceConstraints = [];
        if (0 === $options['province']) {
            $provinceConstraints = [
                new NotBlank(message: 'Seleccione una provincia.'),
            ];
        }

        return $provinceConstraints;
    }

    private function getProvinceQueryBuilder(): \Closure
    {
        return fn (ProvinceRepository $provinceRepository): QueryBuilder => $provinceRepository->findProvincesForForm();
    }

    /**
     * @param array<mixed> $options
     */
    private function getMunicipalityQueryBuilder(array $options): \Closure
    {
        /** @var string $province */
        $province = $options['province'];

        return fn (EntityRepository $er): QueryBuilder => $er->createQueryBuilder('m')->where('m.province = '.$province);
    }
}
