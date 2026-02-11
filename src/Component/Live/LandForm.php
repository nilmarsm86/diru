<?php

namespace App\Component\Live;

use App\Component\Live\Traits\ComponentForm;
use App\Entity\Building;
use App\Entity\Enums\BuildingState;
use App\Entity\Land;
use App\Form\LandType;
use App\Repository\LandRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
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
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\LiveCollectionTrait;

#[AsLiveComponent(template: 'component/live/land_form.html.twig')]
final class LandForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentToolsTrait;
    use ComponentForm;
    use LiveCollectionTrait;

    /**
     * The initial data used to create the form.
     */
    #[LiveProp]
    public ?Land $l = null;

    #[LiveProp]
    public ?string $modal = null;

    #[LiveProp]
    public bool $ajax = false;

    #[LiveProp]
    public ?Building $building = null;

    #[LiveProp]
    public ?string $route = null;

    /** @var string[] */
    #[LiveProp]
    public array $pictureErrors = [];

    /** @var string[] */
    #[LiveProp]
    public array $pdfErrors = [];

    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function mount(?Land $land = null, ?Building $building = null): void
    {
        $this->l = (is_null($land)) ? new Land() : $land;
        $this->building = $building;
    }

    /**
     * @return FormInterface<Land>
     */
    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(LandType::class, $this->l, [
            'building' => $this->building,
        ]);
    }

    #[LiveAction]
    public function save(
        Request $request,
        LandRepository $landRepository,
        ValidatorInterface $validator,
        FileUploader $fileUploader,
    ): ?Response {
        $this->pictureErrors = []; // Limpiar errores previos
        $this->pdfErrors = []; // Limpiar errores previos
        $successMsg = (is_null($this->l?->getId())) ? 'Se han agregado los datos del terreno.' : 'Se han modificado los datos del terreno.'; // TODO: personalizar los mensajes

        $this->submitForm();

        if ($this->isSubmitAndValid()) {
            $land = $this->uploadPhoto($request, $validator, $fileUploader);
            if (null === $land) {
                return null;
            }

            $land = $this->uploadMicrolocation($request, $validator, $fileUploader, $land);
            if (null === $land) {
                return null;
            }

            $this->building?->setLand($land);
            $showFloorMessage = false;
            // cuando se salva los datos del terreno se crean automaticamente la cantidad de plantas
            if (is_null($land->getId())) {
                $showFloorMessage = true;
                //                if (false === (bool) $this->formValues['floor'] || 0 === $this->formValues['occupiedArea']) {//TODO: cambiar
                if (true === (bool) $this->formValues['isNew']) {
                    $land->setFloor(1);
                    $this->building?->setIsNew(true);
                    $this->building?->setState(BuildingState::Design);
                } else {
                    $this->building?->setIsNew(false);
                    $this->building?->setState(BuildingState::Diagnosis);
                }
                $this->building?->createFloors(false, $this->entityManager);
            }

            $landRepository->save($land, true);

            $this->l = new Land();
            if (!is_null($this->modal)) {
                $this->modalManage($land, 'Se han salvado los datos del terreno.', [
                    'land' => $land->getId(),
                ], 'text-bg-success');

                $this->addFlash('success', 'Se han salvado los datos del terreno.');
                if ($showFloorMessage) {
                    $this->addFlash('info', 'Se han creado las plantas del inmueble.');
                }

                return $this->redirectToRoute('app_floor_index', ['building' => $this->building?->getId()], Response::HTTP_SEE_OTHER);
            }

            if ($this->ajax) {
                $this->ajaxManage($land, $successMsg);

                return null;
            }

            $this->addFlash('success', $successMsg);

            return $this->redirectToRoute('app_land_edit', ['id' => $land->getId()], Response::HTTP_SEE_OTHER);
        }

        return null;
    }

    private function getDataModelValue(): string
    {
        return 'norender|*';
    }

    public function isNew(): bool
    {
        if (!is_null($this->l)) {
            return is_null($this->l->getId());
        }

        return true;
    }

    private function uploadPhoto(Request $request, ValidatorInterface $validator, FileUploader $fileUploader, ?Land $land = null): ?Land
    {
        if (null === $land) {
            /** @var Land $land */
            $land = $this->getForm()->getData();
        }

        /** @var UploadedFile|null $photoFile */
        $photoFile = $request->files->all('land')['picture'] ?? null;

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

            $newFilename = $fileUploader->upload($photoFile, '/land/photo');
            if (null !== $newFilename) {
                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $land->setPhoto($newFilename);
            } else {
                $this->pictureErrors[] = 'Ha ocurrido un error al intentar subir el archivo.';
            }
        }

        return $land;
    }

    private function uploadMicrolocation(Request $request, ValidatorInterface $validator, FileUploader $fileUploader, ?Land $land = null): ?Land
    {
        if (null === $land) {
            /** @var Land $land */
            $land = $this->getForm()->getData();
        }

        /** @var UploadedFile|null $pdfFile */
        $pdfFile = $request->files->all('land')['micro'] ?? null;

        // this condition is needed because the 'brochure' field is not required
        // so the PDF file must be processed only when a file is uploaded
        if (true === (bool) $pdfFile) {
            $errors = $validator->validate($pdfFile, [
                new Assert\File(
                    maxSize: '5M',
                    mimeTypes: ['application/pdf'],
                    mimeTypesMessage: 'Formato no válido. Usa PDF.',
                    extensions: ['pdf'],
                    extensionsMessage: 'Por favor suba un archivo válido (PDF).',
                ),
            ]);

            if (count($errors) > 0) {
                foreach ($errors as $error) {
                    $this->pdfErrors[] = $error->getMessage();
                }

                return null;
            }

            $newFilename = $fileUploader->upload($pdfFile, '/land/microlocalization');
            if (null !== $newFilename) {
                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $land->setMicrolocalization($newFilename);
            } else {
                $this->pdfErrors[] = 'Ha ocurrido un error al intentar subir el archivo.';
            }
        }

        return $land;
    }
}
