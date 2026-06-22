<?php

namespace App\Form;

use App\Entity\City;
use App\Entity\Country;
use App\Form\Types\EntityPlusType;
use App\Repository\CountryRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

/**
 * @template TData of City
 *
 * @extends AbstractType<City>
 */
class CityType extends AbstractType
{
    public function __construct(private readonly RouterInterface $router)
    {
    }

    /**
     * @param FormBuilderInterface<City|null> $builder
     * @param array<string, mixed>            $options
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $countryAttr = [
            'class' => Country::class,
            'choice_label' => 'name',
            'label' => 'País:',
            'attr' => [
                'data-model' => 'country',
            ],
            'query_builder' => $this->getCountryQueryBuilder(),
        ];

        $builder
            ->add('name', null, [
                'label' => 'Nombre:',
                'attr' => [
                    'placeholder' => 'Nombre de la ciudad',
                ],
            ]);
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
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => City::class,
            'attr' => [
                'novalidate' => 'novalidate',
            ],
            'modal' => null,
        ]);

        $resolver->setAllowedTypes('modal', ['null', 'string']);
    }

    private function getCountryQueryBuilder(): \Closure
    {
        return fn (CountryRepository $countryRepository): QueryBuilder => $countryRepository->findCountriesForForm();
    }
}
