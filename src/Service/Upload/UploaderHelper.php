<?php

declare(strict_types=1);

namespace App\Service\Upload;

use Gedmo\Sluggable\Util\Urlizer;
use League\Flysystem\FileNotFoundException;
use League\Flysystem\FilesystemInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class UploaderHelper
{
    private const JPG_EXTENSION = 'jpg';

    private FilesystemInterface $filesystem;
    private LoggerInterface $logger;

    public function __construct(
        FilesystemInterface $uploadsFilesystem,
        LoggerInterface $logger
    ) {
        $this->filesystem = $uploadsFilesystem;
        $this->logger = $logger;
    }

    /**
     * @throws \Exception
     */
    public function uploadFile(File $file, $directory)
    {
        if ($file instanceof UploadedFile) {
            $originalFileName = $file->getClientOriginalName();
        } else {
            $originalFileName = $file->getFilename();
        }

        return $this->upload($originalFileName, $file->guessExtension(), $file->getPathname(), $directory);
    }

    /**
     * @throws \Exception
     */
    public function uploadFromSocialNetwork($fileName, $pathToFile, $directory)
    {
        return $this->upload($fileName, self::JPG_EXTENSION, $pathToFile, $directory);
    }

    /**
     * @throws FileNotFoundException
     * @throws \Exception
     */
    public function deleteOldFile(string $path)
    {
        try {
            $result = $this->filesystem->delete($path);

            if (!$result) {
                throw new \Exception(sprintf('Could not delete old uploaded file "%s"', $path));
            }
        } catch (FileNotFoundException $e) {
            $this->logger->alert(sprintf('Old uploaded file "%s" was missing when trying to delete', $path));
        }
    }

    /**
     * @throws \League\Flysystem\FileExistsException
     * @throws \Exception
     */
    private function upload(string $fileName, string $fileExtension, string $pathToFile, string $directory)
    {
        $newFileName =  Urlizer::urlize(pathinfo($fileName, PATHINFO_FILENAME)). '-' .uniqid() . '.' . $fileExtension;

        $stream = fopen($pathToFile, 'r');
        $result = $this->filesystem->writeStream(
            $directory.'/'.$newFileName,
            $stream
        );

        if ($result === false) {
            throw new \Exception(sprintf('Could not write uploaded file "%s"', $newFileName));
        }

        if (is_resource($stream)) {
            fclose($stream);
        }

        return $newFileName;
    }
}