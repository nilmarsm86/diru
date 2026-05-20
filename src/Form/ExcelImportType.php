<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotNull;

/**
 * @template TData of array
 *
 * @extends AbstractType<array>
 */
final class ExcelImportType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('excel', FileType::class, [
                'label' => 'Archivo de salva (*.xls, *.xlsx, *.ods, *.odsx)',
                'mapped' => false,
                'required' => true,
                'attr' => [
                    'accept' => '.xls, .xlsx, .ods, .odsx',
                ],
                'constraints' => [
                    new NotNull(message: 'Debes seleccionar un archivo válido.'),
                    new File(
                        maxSize: '5M',
                        //                        mimeTypes: [
                        //                            'application/x-sqlite3',
                        //                            'application/vnd.sqlite3',
                        //                            'application/octet-stream', // SQLite suele venir así
                        //                        ],
                        mimeTypesMessage: 'Debes seleccionar un archivo de excel válido.',
                    ),
                ],
                'help' => 'Archivos *.xls, *.xlsx, *.ods, *.odsx',
            ]);
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
