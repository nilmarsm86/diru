<?php

namespace App\Service\Pdf;

use Dompdf\Canvas;
use Dompdf\Dompdf;
use Dompdf\FontMetrics;
use Dompdf\Options;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

readonly class PdfGenerator
{
    public function __construct(
        #[Autowire('%kernel.project_dir%')]
        private string $projectDir,
    ) {
    }

    public function generate(string $html): string
    {
        $options = new Options();

        $options->setChroot($this->projectDir.'/public'); // Correcto para Dompdf
        $options->setIsRemoteEnabled(false);                 // Desactiva por seguridad si no lo necesitas
        $options->setDefaultFont('DejaVu Sans');
        $options->setIsHtml5ParserEnabled(true);
        $options->setIsFontSubsettingEnabled(true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $this->addPageFooter($dompdf);

        return $dompdf->output();
    }

    private function addPageFooter(Dompdf $dompdf): void
    {
        $canvas = $dompdf->getCanvas();

        $canvas->page_script(
            function (int $pageNumber, int $pageCount, Canvas $canvas, FontMetrics $fontMetrics) {
                $text = "Página {$pageNumber} de {$pageCount}";
                $font = (string) $fontMetrics->getFont('DejaVu Sans', 'normal');

                // Centrado horizontal
                $width = $fontMetrics->getTextWidth($text, $font, 10);
                $canvas->text(
                    565 - $width,   // A4 width ≈ 595pt
                    820,
                    $text,
                    $font,
                    10,
                    [0.5, 0.5, 0.5]
                );
            }
        );
    }
}
