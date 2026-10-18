<?php

namespace App\Shared\Infrastructure\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Process\Process;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final class ImageConverter
{
    private const MAX_SIZE = 15 * 1024 * 1024; // 15 Mo
    private const MAX_DIMENSION = 2000;
    private const QUALITY = 82;

    public function __construct(
        #[Autowire('%kernel.project_dir%')]
        private readonly string $projectDir,
    ) {}

    public function convertToWebp(UploadedFile $file, string $folder): string
    {
        if (!$file->isValid()) {
            throw new \InvalidArgumentException('Le fichier envoyé est invalide.');
        }

        if ($file->getSize() > self::MAX_SIZE) {
            throw new \InvalidArgumentException(
                'Chaque image doit faire 15 Mo maximum.'
            );
        }

        $allowedMimeTypes = [
            'image/jpeg',
            'image/png',
            'image/webp',
            'image/heic',
            'image/heif',
            'image/avif',
            'image/bmp',
            'image/tiff',
            'image/gif',
        ];

        if (!in_array($file->getMimeType(), $allowedMimeTypes, true)) {
            throw new \InvalidArgumentException(
                'Ce format d’image n’est pas pris en charge.'
            );
        }

        // Dossier de destination autorisé
        $allowedFolders = ['covers', 'images', 'claims'];

        if (!in_array($folder, $allowedFolders, true)) {
            throw new \InvalidArgumentException('Dossier non autorisé.');
        }

        $directory = $this->projectDir . '/public/uploads/' . $folder;

        if (!is_dir($directory)
            && !mkdir($directory, 0755, true)
            && !is_dir($directory)
        ) {
            throw new \RuntimeException(
                'Impossible de créer le dossier des images.'
            );
        }

        $filename = bin2hex(random_bytes(16)) . '.webp';
        $destination = $directory . '/' . $filename;

        $process = new Process([
            'magick',
            $file->getPathname(),
            '-auto-orient',
            '-thumbnail',
            self::MAX_DIMENSION . 'x' . self::MAX_DIMENSION . '>',
            '-strip',
            '-quality',
            (string) self::QUALITY,
            $destination,
        ]);

        $process->setTimeout(30);

        try {
            $process->mustRun();

            if (!is_file($destination) || filesize($destination) === 0) {
                throw new \RuntimeException(
                    'La conversion de l’image a échoué.'
                );
            }

            return $filename;
        } catch (\Throwable $exception) {
            if (is_file($destination)) {
                unlink($destination);
            }

            throw new \RuntimeException(
                'Impossible de traiter cette image. Vérifiez son format.',
                0,
                $exception
            );
        }
    }
}
