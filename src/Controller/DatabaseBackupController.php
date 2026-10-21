<?php

namespace App\Controller;

use App\Form\DatabaseImportType;
use App\Service\Backup\DatabaseBackupException;
use App\Service\Backup\DatabaseBackupService;
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
            $this->addFlash('danger', 'Revise la configuración importada.');

            return $this->redirectToRoute('database_backup_index');
        }

        try {
            $backup = $form->get('database')->getData();
            assert($backup instanceof UploadedFile);
            $this->backupService->replaceWithUpload($backup);
            $icon = '<svg viewBox="0 0 1568 1280" fill="currentColor" height="1em" width="1em" class="bi" aria-hidden="true" style="color: white"><path fill="currentColor" d="M640 1184q0 4 1 20t.5 26.5t-3 23.5t-10 19.5t-20.5 6.5H288q-119 0-203.5-84.5T0 992V288Q0 169 84.5 84.5T288 0h320q13 0 22.5 9.5T640 32q0 4 1 20t.5 26.5t-3 23.5t-10 19.5T608 128H288q-66 0-113 47t-47 113v704q0 66 47 113t113 47h312l11.5 1l11.5 3l8 5.5l7 9zm928-544q0 26-19 45l-544 544q-19 19-45 19t-45-19t-19-45V896H448q-26 0-45-19t-19-45V448q0-26 19-45t45-19h448V96q0-26 19-45t45-19t45 19l544 544q19 19 19 45"></path></svg>';

            $this->addFlash('success', 'Configuración importada correctamente.<br><br><a href="'.$this->generateUrl('app_logout').'">'.$icon.' <strong style="color:white">Salir</strong></a>');
        } catch (DatabaseBackupException $e) {
            $this->addFlash('danger', $e->getMessage());
        }

        return $this->redirectToRoute('database_backup_index');
    }
}
