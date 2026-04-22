<?php

namespace App\Controller;

use App\Form\DatabaseImportType;
use App\Service\DatabaseBackupException;
use App\Service\DatabaseBackupService;
use Doctrine\DBAL\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[Route('/database/backup', name: 'database_backup_')]
final class DatabaseBackupController extends AbstractController
{
    public function __construct(
        private readonly DatabaseBackupService $backupService,
    ) {
    }

    #[Route('', name: 'index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('database_backup/index.html.twig', [
            'import_form' => $this->createForm(DatabaseImportType::class)->createView(),
        ]);
    }

    #[Route('/export', name: 'export', methods: ['POST'])]
    public function export(Request $request): Response
    {
        if (!$this->isCsrfTokenValid('export-db', (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF inválido.');
        }

        try {
            $snapshotPath = $this->backupService->exportToTempFile();
        } catch (DatabaseBackupException|Exception $e) {
            $this->addFlash('danger', $e->getMessage());

            return $this->redirectToRoute('database_backup_index');
        }

        $response = $this->file($snapshotPath);
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            sprintf('backup-%s.db', date('Y-m-d-His')),
        );
        $response->deleteFileAfterSend(true);

        return $response;
    }

    #[Route('/import', name: 'import', methods: ['POST'])]
    public function import(Request $request): Response
    {
        $form = $this->createForm(DatabaseImportType::class);
        $form->handleRequest($request);

        if (!$form->isSubmitted() || !$form->isValid()) {
            $this->addFlash('danger', 'Formulario inválido. Revisa el archivo seleccionado.');

            return $this->redirectToRoute('database_backup_index');
        }

        try {
            $backup = $form->get('database')->getData();
            assert($backup instanceof UploadedFile);
            $this->backupService->replaceWithUpload($backup);
            $this->addFlash('success', 'Base de datos importada correctamente.');
        } catch (DatabaseBackupException $e) {
            $this->addFlash('danger', $e->getMessage());
        }

        return $this->redirectToRoute('database_backup_index');
    }
}
