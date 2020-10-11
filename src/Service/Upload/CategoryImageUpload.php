<?php

declare(strict_types=1);

namespace App\Service\Upload;

use App\Model\Category\Domain\Service\IImageUploader;
use League\Flysystem\FileNotFoundException;
use League\Flysystem\FilesystemInterface;
use Psr\Log\LoggerInterface;

class CategoryImageUpload implements IImageUploader
{
    private const CATEGORY_DIRECTORY = 'category';

    private UploaderHelper $uploaderHelper;
    private FilesystemInterface $filesystem;
    private LoggerInterface $logger;

    public function __construct(
        UploaderHelper $uploaderHelper,
        FilesystemInterface $uploadsFilesystem,
        LoggerInterface $logger
    ) {
        $this->uploaderHelper = $uploaderHelper;
        $this->filesystem = $uploadsFilesystem;
        $this->logger = $logger;
    }

    /**
     * @throws FileNotFoundException
     * @throws \Exception
     */
    public function upload($file, ?string $existingFilename = null): string
    {
        $newFilename = $this->uploaderHelper->uploadFile($file, self::CATEGORY_DIRECTORY);

        !$existingFilename ?:$this->uploaderHelper->deleteOldFile(self::CATEGORY_DIRECTORY.'/'.$existingFilename);

        return $newFilename;
    }
}