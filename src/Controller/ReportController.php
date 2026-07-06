<?php

namespace App\Controller;

use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ReportController extends AbstractController
{
    #[Route('/reporte/pdf', name: 'reporte_pdf')]
    public function pdf(): Response
    {
        $options = new Options();
        $options->setChroot($_SERVER['DOCUMENT_ROOT']); // Para que cargue imágenes y CSS
        $options->setIsRemoteEnabled(true);
        $options->setDefaultFont('DejaVu Sans'); // Mejor soporte UTF-8

        $logoPath = $_SERVER['DOCUMENT_ROOT'] . 'icono.jpg';
        $logoData = base64_encode(file_get_contents($logoPath));
        $logoBase64 = 'data:image/png;base64,' . $logoData;

        $dompdf = new Dompdf($options);

        $html = $this->renderView('report/index.html.twig', [
            'titulo' => 'Reporte Mensual',
            'fecha' => new \DateTime(),
            'items' => [], // aquí pasas las filas de la tabla
            'logo' => $logoBase64,
        ]);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Pie de página con número de página (opcional)
        $canvas = $dompdf->getCanvas();
        $canvas->page_script(function ($pageNumber, $pageCount, $canvas, $fontMetrics) {
            $text = "Página $pageNumber de $pageCount";
            $canvas->text(510, 820, $text, null, 10, [0.5, 0.5, 0.5]);
        });

        return new Response(
            $dompdf->output(),
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="reporte.pdf"', // attachment si quieres forzar descarga
            ]
        );
    }

    #[Route('/reporte/preview', name: 'reporte_preview')]
    public function preview(): Response
    {
        $logoPath = $_SERVER['DOCUMENT_ROOT'] . 'icono.jpg';
        $logoData = base64_encode(file_get_contents($logoPath));
        $logoBase64 = 'data:image/png;base64,' . $logoData;

        return $this->render('report/index.html.twig', [
            'titulo' => 'Reporte Mensual',
            'fecha' => new \DateTime(),
            'items' => [], // aquí pasas las filas de la tabla
            'logo' => $logoBase64,
        ]);
    }
}
