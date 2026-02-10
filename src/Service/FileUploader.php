<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploader
{
    public function __construct(
        private SluggerInterface $slugger,
        #[Autowire('%kernel.project_dir%/public/uploads')] private string $uploadsDirectory,
    ) {
    }

    public function upload(UploadedFile $file, string $targetDirectory): ?string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        // this is needed to safely include the file name as part of the URL
        $safeFileName = $this->slugger->slug($originalFilename);
        $fileName = $safeFileName.'-'.uniqid().'.'.$file->guessExtension();

        try {
            $file->move($this->uploadsDirectory.$targetDirectory, $fileName);
        } catch (FileException $e) {
            return null;
        }

        return $fileName;
    }
}
