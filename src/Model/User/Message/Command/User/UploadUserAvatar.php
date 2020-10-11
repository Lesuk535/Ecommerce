<?php

declare(strict_types=1);

namespace App\Model\User\Message\Command\User;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;


class UploadUserAvatar
{
    /**
     * @Assert\Image()
     */
    public UploadedFile $uploadedFile;
    public string $email;
    public ?string $existingFilename;

    public function __construct(UploadedFile $uploadedFile, string $email, ?string $existingFilename)
    {
        $this->uploadedFile = $uploadedFile;
        $this->email = $email;
        $this->existingFilename = $existingFilename;
    }
}