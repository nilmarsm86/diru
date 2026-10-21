<?php

namespace App\Controller\Traits;

use App\DTO\Paginator;
use App\Service\Pdf\PdfAssetManager;
use App\Service\Pdf\PdfGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

trait PdfResponseTrait
{
    public function pdfResponse(string $pdfContent, string $filename): Response
    {
        $disposition = (new ResponseHeaderBag())->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $filename.'-'.date('Y-m-d_H-i').'.pdf',
            'documento-'.date('Y-m-d_H-i').'.pdf' // fallback ASCII
        );

        return new Response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => $disposition,
            'Content-Length' => strlen($pdfContent),
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, must-revalidate',
        ]);
    }

    /**
     * @param array<mixed> $vars
     */
    public function renderPdf(
        mixed $filter,
        Paginator $paginator,
        PdfAssetManager $pdfAssetManager,
        PdfGenerator $pdfGenerator,
        string $template,
        string $title,
        string $fileName,
        array $vars = [],
    ): Response {
        $html = $this->renderView($template, [
            'filter' => $filter,
            'paginator' => $paginator,
            'logo' => $pdfAssetManager->getLogoBase64(),
            'title' => $title,
        ] + $vars);

        $pdfContent = $pdfGenerator->generate($html);

        return $this->pdfResponse($pdfContent, $fileName);
    }
}
