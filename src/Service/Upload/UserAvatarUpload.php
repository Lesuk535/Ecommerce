<?php

declare(strict_types=1);

namespace App\Service\Upload;

use App\Model\User\Domain\Service\IAvatarUploader;
use League\Flysystem\FileNotFoundException;
use League\Flysystem\FilesystemInterface;
use Psr\Log\LoggerInterface;

final class UserAvatarUpload implements IAvatarUploader
{
    private const USER_AVATAR = 'user_avatar';

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
    public function uploadUserAvatar($file, ?string $existingFilename = null): string
    {
        if ($file instanceof OAuthAvatar) {
            $newFilename = $this->uploaderHelper->uploadFromSocialNetwork($file->getPictureName(), $file->getPictureUrl(),self::USER_AVATAR,);
        } else {
            $newFilename = $this->uploaderHelper->uploadFile($file, self::USER_AVATAR);
        }

        !$existingFilename ?:$this->uploaderHelper->deleteOldFile(self::USER_AVATAR.'/'.$existingFilename);

//        if ($existingFilename) {
//            try {
//                $result = $this->filesystem->delete(self::USER_AVATAR.'/'.$existingFilename);
//
//                if ($result === false) {
//                    throw new \Exception(sprintf('Could not delete old uploaded file "%s"', $existingFilename));
//                }
//            } catch (FileNotFoundException $e) {
//                $this->logger->alert(sprintf('Old uploaded file "%s" was missing when trying to delete', $existingFilename));
//            }
//        }

        return $newFilename;
    }
}