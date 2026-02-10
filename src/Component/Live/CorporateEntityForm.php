<?php

namespace App\Component\Live;

use App\Component\Live\Traits\ComponentForm;
use App\Entity\CorporateEntity;
use App\Form\CorporateEntityType;
use App\Repository\CorporateEntityRepository;
use App\Repository\MunicipalityRepository;
use App\Repository\OrganismRepository;
use App\Repository\ProvinceRepository;
use App\Service\FileUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(template: 'component/live/corporate_entity_form.html.twig')]
final class CorporateEntityForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;
    use ComponentToolsTrait;
    use ComponentForm;

    /**
     * The initial data used to create the form.
     */
    #[LiveProp]
    public ?CorporateEntity $ce = null;

    #[LiveProp]
    public ?string $modal = null;

    #[LiveProp]
    public bool $ajax = false;

    #[LiveProp(writable: true)]
    public ?int $organism = 0;

    #[LiveProp(writable: true)]
    public ?string $street = '';

    #[LiveProp(writable: true)]
    public int $province = 0;

    #[LiveProp(writable: true)]
    public int $municipality = 0;

    /** @var string[] */
    #[LiveProp]
    public array $pictureErrors = [];

    public function __construct(
        protected readonly ProvinceRepository $provinceRepository,
        protected readonly MunicipalityRepository $municipalityRepository,
    ) {
    }

    public function mount(?CorporateEntity $ce = null): void
    {
        $this->ce = $ce;
        if (is_null($this->ce)) {
            $this->ce = new CorporateEntity();
        }
    }

    public function preValue(): void
    {
        /** @var array<string, array<string, mixed>> $formValues */
        $formValues = $this->formValues;

        if (0 !== $this->organism) {
            $formValues['organism'] = (string) $this->organism;
            $this->organism = 0;
            $this->formValues = $formValues;
        }

        if ('' !== $this->street) {
            /** @var array<string, mixed> $streetAddress */
            $streetAddress = $formValues['streetAddress'] ?? [];
            $streetAddress['street'] = (string) $this->street;
            $formValues['streetAddress'] = $streetAddress;
            $this->street = '';
            $this->formValues = $formValues;
        }

        if (0 !== $this->province) {
            /** @var array<string, mixed> $streetAddress */
            $streetAddress = $formValues['streetAddress'] ?? [];
            /** @var array<string, mixed> $address */
            $address = $streetAddress['address'] ?? [];
            $address['province'] = (string) $this->province;
            $streetAddress['address'] = $address;
            $formValues['streetAddress'] = $streetAddress;
            $this->province = 0;
            $this->formValues = $formValues;
        }

        if (0 !== $this->municipality) {
            /** @var array<string, mixed> $streetAddress */
            $streetAddress = $formValues['streetAddress'] ?? [];
            /** @var array<string, mixed> $address */
            $address = $streetAddress['address'] ?? [];
            $address['municipality'] = (string) $this->municipality;
            $streetAddress['address'] = $address;
            $formValues['streetAddress'] = $streetAddress;
            $this->municipality = 0;
            $this->formValues = $formValues;
        } else {
            /** @var array<string, array<string, array<int, mixed>>> $formValues */
            $formValues = $this->formValues;
            if (isset($formValues['streetAddress']['address'])) {
                if (isset($formValues['streetAddress']['address']['province'])) {
                    if ($formValues['streetAddress']['address']['municipality']) {
                        $mun = $this->municipalityRepository->find($formValues['streetAddress']['address']['municipality']);
                        if ((string) $mun?->getProvince()?->getId() !== $formValues['streetAddress']['address']['province']) {
                            $prov = $this->provinceRepository->find($formValues['streetAddress']['address']['province']);
                            if (!is_null($prov)) {
                                $formValues['streetAddress']['address']['municipality'] = ($prov->getMunicipalities()->count() > 0 && false !== $prov->getMunicipalities()->first())
                                    ? (string) $prov->getMunicipalities()->first()->getId()
                                    : '';
                            }
                        }
                    } else {
                        $prov = $this->provinceRepository->find($formValues['streetAddress']['address']['province']);
                        if (!is_null($prov)) {
                            if ($prov->getMunicipalities()->count() > 0 && false !== $prov->getMunicipalities()->first()) {
                                $formValues['streetAddress']['address']['municipality'] = (string) $prov->getMunicipalities()->first()->getId();
                            }
                        }
                    }
                }
            }

            $this->formValues = $formValues;
        }
    }

    /**
     * @return FormInterface<CorporateEntity>
     */
    protected function instantiateForm(): FormInterface
    {
        $this->preValue();

        /** @var array<string, array<string, array<string, mixed>>> $formValues */
        $formValues = $this->formValues;
        $province = 0;
        $municipality = 0;

        if (null === $this->ce?->getId()) {
            if (isset($formValues['streetAddress']['address'])) {
                /** @var int $province */
                $province = $formValues['streetAddress']['address']['province'] ?? 0;
                /** @var int $municipality */
                $municipality = $formValues['streetAddress']['address']['municipality'] ?? 0;
            }
            if (isset($formValues['streetAddress']['street'])) {
                $street = $formValues['streetAddress']['street'];
            }
        } else {
            $mun = $this->ce->getMunicipality();
            /** @var int $province */
            $province = $formValues['streetAddress']['address']['province'] ?? $mun?->getProvince()?->getId();
            /** @var int $municipality */
            $municipality = $formValues['streetAddress']['address']['municipality'] ?? $mun?->getId();
            /** @var string $street */
            $street = $formValues['streetAddress']['street'] ?? $this->ce->getStreet();
        }

        return $this->createForm(CorporateEntityType::class, $this->ce, [
            'street' => $street ?? '',
            'province' => (int) $province,
            'municipality' => (int) $municipality,
            'live_form' => ('on(change)|*' === $this->getDataModelValue()),
            'modal' => $this->modal,
        ]);
    }

    #[LiveAction]
    public function save(
        CorporateEntityRepository $corporateEntityRepository,
        OrganismRepository $organismRepository,
        ValidatorInterface $validator,
        FileUploader $fileUploader,
        Request $request,
    ): ?Response {
        $this->preValue();

        /** @var array<string, array<string, mixed>> $formValues */
        $formValues = $this->formValues;

        $successMsg = (is_null($this->ce?->getId())) ? 'Se ha agregado la entidad corporativa.' : 'Se ha modificado la entidad corporativa.'; // TODO: personalizar los mensajes

        $this->submitForm();

        if ($this->isSubmitAndValid()) {
            //            /** @var CorporateEntity $ce */
            //            $ce = $this->getForm()->getData();

            $ce = $this->uploadPhoto($request, $validator, $fileUploader);
            if (null === $ce) {
                return null;
            }

            $organism = $organismRepository->find($formValues['organism']);
            $ce->setOrganism($organism);

            /** @var string $street */
            $street = $formValues['streetAddress']['street'];
            $ce->setStreet($street);

            /** @var array<string, array<string, array<string, mixed>>> $formValues */
            $formValues = $this->formValues;
            $municipality = $this->municipalityRepository->find($formValues['streetAddress']['address']['municipality']);
            $ce->setMunicipality($municipality);

            $corporateEntityRepository->save($ce, true);

            $this->ce = new CorporateEntity();
            if (!is_null($this->modal)) {
                $this->modalManage($ce, 'Se ha seleccionado la nueva entidad corporativa agregada.', [
                    'corporateEntity' => $ce->getId(),
                ]);

                return null;
            }

            if ($this->ajax) {
                $this->ajaxManage($ce, $successMsg);

                return null;
            }

            $this->addFlash('success', $successMsg);

            return $this->redirectToRoute('app_corporate_entity_index', [], Response::HTTP_SEE_OTHER);
        }

        return null;
    }

    private function getDataModelValue(): string
    {
        return 'norender|*';
    }

    private function uploadPhoto(Request $request, ValidatorInterface $validator, FileUploader $fileUploader, ?CorporateEntity $ce = null): ?CorporateEntity
    {
        if (null === $ce) {
            /** @var CorporateEntity $ce */
            $ce = $this->getForm()->getData();
        }

        /** @var UploadedFile|null $photoFile */
        $photoFile = $request->files->all('corporate_entity')['picture'] ?? null;

        // this condition is needed because the 'brochure' field is not required
        // so the PDF file must be processed only when a file is uploaded
        if (true === (bool) $photoFile) {
            $errors = $validator->validate($photoFile, [
                new Assert\File(
                    maxSize: '5M',
                    mimeTypes: ['image/jpeg', 'image/png'],
                    mimeTypesMessage: 'Formato no válido. Usa JPG o PNG.',
                    extensions: ['jpg', 'jpeg', 'png'],
                    extensionsMessage: 'Por favor suba una imagen válida (JPG, JPEG o PNG).',
                ),
            ]);

            if (count($errors) > 0) {
                foreach ($errors as $error) {
                    $this->pictureErrors[] = $error->getMessage();
                }

                return null;
            }

            $newFilename = $fileUploader->upload($photoFile, '/corporate_entity/logo');
            if (null !== $newFilename) {
                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $ce->setLogo($newFilename);
            } else {
                $this->pictureErrors[] = 'Ha ocurrido un error al intentar subir el archivo.';
            }
        }

        return $ce;
    }
}
