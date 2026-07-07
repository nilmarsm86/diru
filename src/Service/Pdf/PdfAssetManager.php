<?php

namespace App\Service\Pdf;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

readonly class PdfAssetManager
{
    public function __construct(
        #[Autowire('%kernel.project_dir%')]
        private string $projectDir,

        private Filesystem $filesystem,
    ) {
    }

    public function getLogoBase64(): string
    {
        // Usar Path::join para manejar correctamente Windows y Linux
        $path = Path::join($this->projectDir, 'public', 'icono.jpg');

        if (!$this->filesystem->exists($path)) {
            throw new \RuntimeException('Logo no encontrado en: '.$path);
        }

        return $this->encodeBase64Image($path);
    }

    private function encodeBase64Image(string $imagePath): string
    {
        $content = file_get_contents($imagePath);

        if (false === $content) {
            throw new \RuntimeException('No se pudo leer el logo: '.$imagePath);
        }

        $logoData = base64_encode($content);

        $extension = strtolower(pathinfo($imagePath, PATHINFO_EXTENSION));
        $mime = match ($extension) {
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            default => 'image/png',
        };

        return "data:{$mime};base64,{$logoData}";
    }
}
