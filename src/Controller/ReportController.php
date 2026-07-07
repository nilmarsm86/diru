<?php

namespace App\Controller;

use App\Service\Pdf\PdfAssetManager;
use App\Service\Pdf\PdfGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ReportController extends AbstractController
{
    #[Route('/reporte/pdf', name: 'reporte_pdf')]
    public function pdf(PdfGenerator $pdfGenerator, PdfAssetManager $pdfAssetManager): Response
    {
        $html = $this->renderView('report/index.html.twig', [
            'titulo' => 'Reporte Mensual',
            'fecha' => new \DateTime(),
            'items' => [], // aquí pasas las filas de la tabla
            'logo' => $pdfAssetManager->getLogoBase64(),
        ]);

        $pdfContent = $pdfGenerator->generate($html);

        return new Response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="reporte-'.date('Y-m-d_H-i').'.pdf"',
            'Content-Length' => strlen($pdfContent),
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, must-revalidate',
        ]);
    }

    #[Route('/reporte/preview', name: 'reporte_preview')]
    public function preview(PdfAssetManager $pdfAssetManager): Response
    {
        return $this->render('report/index.html.twig', [
            'titulo' => 'Reporte Mensual',
            'fecha' => new \DateTime(),
            'items' => [], // aquí pasas las filas de la tabla
            'logo' => $pdfAssetManager->getLogoBase64(),
        ]);
    }
}
