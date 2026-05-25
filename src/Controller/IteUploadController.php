<?php

namespace App\Controller;

use App\Form\ExcelImportType;
use App\Service\IteImport\IteImportService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[Route('/ite/upload', name: 'ite_upload_')]
final class IteUploadController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('ite_upload/index.html.twig', [
            'import_form' => $this->createForm(ExcelImportType::class)->createView(),
        ]);
    }

    #[Route('/import', name: 'import', methods: ['POST'])]
    public function import(Request $request, IteImportService $iteImportService): Response
    {
        $form = $this->createForm(ExcelImportType::class);
        $form->handleRequest($request);

        if (!$form->isSubmitted() || !$form->isValid()) {
            $this->addFlash('danger', 'Formulario inválido. Revisa el archivo seleccionado.');

            return $this->redirectToRoute('ite_upload_index');
        }

        try {
            $excel = $form->get('excel')->getData();
            assert($excel instanceof UploadedFile);
            $result = $iteImportService->import($excel->getRealPath());

            $msg = '<strong>'.$result->getImportedCount().'</strong> referencias importadas correctamente.';
            $this->addFlash('success', $msg);

            $skippedSheets = $result->getSkippedSheets();
            if (count($skippedSheets) > 0) {
                $msg = ' Se a saltado la(s) hoja(s) '.join(', ', $skippedSheets).', por estar vacía(s).';
                $this->addFlash('info', $msg);
            }

            if ($result->getErrorCount() > 0) {
                $this->addFlash('warning', 'Ocurrieron algunos errores durante la importación.');
            }
        } catch (\Exception $e) {
            $this->addFlash('danger', $e->getMessage());
        }

        return $this->redirectToRoute('ite_upload_index');
    }
}
