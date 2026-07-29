<?php

namespace App\Controller\Traits;

use Symfony\Component\HttpFoundation\Response;

trait PdfResponseTrait
{
    public function pdfResponse(string $pdfContent, string $filename): Response
    {
        return new Response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$filename.'-'.date('Y-m-d_H-i').'.pdf"',
            'Content-Length' => strlen($pdfContent),
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, must-revalidate',
        ]);
    }
}
