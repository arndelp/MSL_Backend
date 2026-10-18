<?php

namespace App\Shared\Infrastructure\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class ImageFileManager
{
    private string $uploadsDirectory;

    public function __construct(
        KernelInterface $kernel,
        private LoggerInterface $logger,
    ) {
        $this->uploadsDirectory = $kernel->getProjectDir()
            . '/public/uploads';
    }

    public function deleteBookImages(?string $cover, ?array $images): void
    {
        if ($cover !== null && $cover !== '') {
            $this->deleteFile('covers', $cover);
        }

        foreach ($images ?? [] as $image) {
            if (is_string($image) && $image !== '') {
                $this->deleteFile('images', $image);
            }
        }
    }

    private function deleteFile(string $folder, string $filename): void
    {
        // Empêche de cibler un fichier en dehors du dossier autorisé.
        $filename = basename($filename);

        $path = $this->uploadsDirectory . '/' . $folder . '/' . $filename;

        if (!is_file($path)) {
            return;
        }

        if (!unlink($path)) {
            $this->logger->warning(
                'Impossible de supprimer un fichier image.',
                ['path' => $path]
            );
        }
    }
}
