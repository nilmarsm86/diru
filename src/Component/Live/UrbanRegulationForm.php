<?php

namespace App\Component\Live;

use App\Component\Live\Traits\ComponentForm;
use App\Entity\UrbanRegulation;
use App\Form\UrbanRegulationType;
use App\Repository\UrbanRegulationRepository;
use App\Repository\UrbanRegulationTypeRepository;
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

#[AsLiveComponent(template: 'component/live/urban_regulation_form.html.twig')]
final class UrbanRegulationForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;
    use ComponentToolsTrait;
    use ComponentForm;

    /**
     * The initial data used to create the form.
     */
    #[LiveProp]
    public ?UrbanRegulation $ur = null;

    #[LiveProp]
    public ?string $modal = null;

    #[LiveProp]
    public bool $ajax = false;

    #[LiveProp]
    public UrbanRegulation $entity;

    #[LiveProp(writable: true)]
    public ?int $type = 0;

    /** @var string[] */
    #[LiveProp]
    public array $pictureErrors = [];

    public function mount(?UrbanRegulation $ur = null): void
    {
        $this->ur = (is_null($ur)) ? new UrbanRegulation() : $ur;
        $this->entity = $this->ur;
    }

    public function preValue(): void
    {
        if (0 !== $this->type) {
            $this->formValues['type'] = (string) $this->type;
            $this->type = 0;
        }
    }

    /**
     * @return FormInterface<UrbanRegulation>
     */
    protected function instantiateForm(): FormInterface
    {
        $this->preValue();

        return $this->createForm(UrbanRegulationType::class, $this->ur);
    }

    #[LiveAction]
    public function save(
        UrbanRegulationRepository $urbanRegulationRepository,
        UrbanRegulationTypeRepository $urbanRegulationTypeRepository,
        ValidatorInterface $validator,
        FileUploader $fileUploader,
        Request $request,
    ): ?Response {
        $this->pictureErrors = []; // Limpiar errores previos
        $this->preValue();

        $successMsg = (is_null($this->ur?->getId())) ? 'Se ha agregado la regulación urbana.' : 'Se ha modificado la regulación urbana.'; // TODO: personalizar los mensajes

        $this->submitForm();

        if ($this->isSubmitAndValid()) {
            $ur = $this->uploadPhoto($request, $validator, $fileUploader);
            if (null === $ur) {
                return null;
            }

            if ('' !== $this->formValues['type']) {
                $type = $urbanRegulationTypeRepository->find($this->formValues['type']);
                $ur->setType($type);
            }

            $urbanRegulationRepository->save($ur, true);

            $this->ur = new UrbanRegulation();
            $this->entity = $this->ur;
            if (!is_null($this->modal)) {
                $this->modalManage($ur, 'Se ha seleccionado la regulación urbana.', [
                    'urbanRegulation' => $ur->getId(),
                ]);

                return null;
            }

            if ($this->ajax) {
                $this->ajaxManage($ur, $successMsg);

                return null;
            }

            $this->addFlash('success', $successMsg);

            return $this->redirectToRoute('app_urban_regulation_index', [], Response::HTTP_SEE_OTHER);
        }

        return null;
    }

    private function getDataModelValue(): string
    {
        return 'norender|*';
    }

    private function uploadPhoto(Request $request, ValidatorInterface $validator, FileUploader $fileUploader, ?UrbanRegulation $ur = null): ?UrbanRegulation
    {
        if (null === $ur) {
            /** @var UrbanRegulation $ur */
            $ur = $this->getForm()->getData();
        }

        /** @var UploadedFile|null $photoFile */
        $photoFile = $request->files->all('urban_regulation')['picture'] ?? null;

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

            $newFilename = $fileUploader->upload($photoFile, '/urban_regulation/photo');
            if (null !== $newFilename) {
                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $ur->setPhoto($newFilename);
            } else {
                $this->pictureErrors[] = 'Ha ocurrido un error al intentar subir el archivo.';
            }
        }

        return $ur;
    }
}
