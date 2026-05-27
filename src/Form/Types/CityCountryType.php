<?php

namespace App\Form\Types;

use App\Entity\City;
use App\Entity\Country;
use App\Repository\CityRepository;
use App\Repository\CountryRepository;
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
class CityCountryType extends AbstractType
{
    public function __construct(
        private readonly CountryRepository $countryRepository,
        private readonly CityRepository $cityRepository,
        private readonly RouterInterface $router,
    ) {
    }

    /**
     * @param FormBuilderInterface<array|null> $builder
     * @param array<string, mixed>             $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $country = $this->countryRepository->find($options['country']);
        $city = $this->cityRepository->find($options['city']);

        $countryAttr = [
            'class' => Country::class,
            'placeholder' => (bool) $options['country'] ? null : '-Seleccione-',
            'label' => 'País:',
            'mapped' => false,
            'constraints' => $this->getCountryConstraints($options),
            'data' => $country,
            'query_builder' => $this->getCountryQueryBuilder(),
        ];

        if (is_null($options['modal'])) {
            $builder->add('country', EntityPlusType::class, [
                'add' => true,
                'add_title' => 'Agregar País',
                'add_id' => 'modal-load',
                'add_url' => $this->router->generate('app_country_new', ['modal' => 'modal-load']),
            ] + $countryAttr);
        } else {
            $builder->add('country', EntityType::class, [] + $countryAttr);
        }

        $cityAttr = [
            'class' => City::class,
            'placeholder' => (bool) $options['city'] ? null : '-Seleccione un país-',
            'label' => 'Ciudad:',
            'constraints' => $this->getCityConstraints($options),
            'data' => $city,
            'query_builder' => $this->getCityQueryBuilder($options),
        ];

        if (is_null($options['modal'])) {
            $builder->add('city', EntityPlusType::class, [
                'add' => true,
                'add_title' => 'Agregar Ciudad',
                'add_id' => 'modal-load',
                'add_url' => $this->router->generate('app_city_new', ['modal' => 'modal-load']),
            ] + $cityAttr);
        } else {
            $builder->add('city', EntityType::class, [] + $cityAttr);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'country' => 0,
            'city' => 0,
            'row' => false,
            'col' => true,
            'live_form' => false,
            'modal' => null,
        ]);

        $resolver->setAllowedTypes('country', ['int']);
        $resolver->setAllowedTypes('city', ['int']);
        $resolver->setAllowedTypes('row', 'bool');
        $resolver->setAllowedTypes('col', 'bool');
        $resolver->setAllowedTypes('live_form', 'bool');
        $resolver->setAllowedTypes('modal', ['null', 'string']);
    }

    /**
     * @param FormInterface<array> $form
     * @param array<string, mixed> $options
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
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
    private function getCityConstraints(array $options): array
    {
        $cityConstraints = [];
        if (0 === $options['city']) {
            $cityConstraints = [
                new NotBlank(message: 'Seleccione una ciudad.'),
            ];
        }

        return $cityConstraints;
    }

    /**
     * @param array<mixed> $options
     *
     * @return array<mixed>|NotBlank[]
     */
    private function getCountryConstraints(array $options): array
    {
        $countryConstraints = [];
        if (0 === $options['country']) {
            $countryConstraints = [
                new NotBlank(message: 'Seleccione un país.'),
            ];
        }

        return $countryConstraints;
    }

    private function getCountryQueryBuilder(): \Closure
    {
        return fn (CountryRepository $countryRepository): QueryBuilder => $countryRepository->findCountriesForForm();
    }

    /**
     * @param array<mixed> $options
     */
    private function getCityQueryBuilder(array $options): \Closure
    {
        /** @var string $country */
        $country = $options['country'];

        return fn (EntityRepository $er): QueryBuilder => $er->createQueryBuilder('city')->where('city.country = '.$country);
    }
}
