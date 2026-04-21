<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotNull;

/**
 * @template TData of array
 *
 * @extends AbstractType<array>
 */
final class DatabaseImportType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('database', FileType::class, [
                'label' => 'Archivo de salva (*.sqlite, *.db)',
                'mapped' => false,
                'required' => true,
                'attr' => [
                    'accept' => '.sqlite,.db',
                ],
                'constraints' => [
                    new NotNull(message: 'Debes seleccionar un archivo de salva.'),
                    new File(
                        maxSize: '200M',
                        mimeTypes: [
                            'application/x-sqlite3',
                            'application/vnd.sqlite3',
                            'application/octet-stream', // SQLite suele venir así
                        ],
                        mimeTypesMessage: 'El archivo debe ser una salva correcta.',
                    ),
                ],
                'help' => 'Imágenes *.sqlite, *.db',
            ]);
        //            ->add('submit', SubmitType::class, ['label' => 'Importar y reemplazar']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'attr' => [
                'novalidate' => 'novalidate',
            ],
        ]);
    }
}
