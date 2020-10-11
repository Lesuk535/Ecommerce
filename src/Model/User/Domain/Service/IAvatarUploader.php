<?php

declare(strict_types=1);

namespace App\Model\User\Domain\Service;

interface IAvatarUploader
{
    public function uploadUserAvatar($file, ?string $existingFilename = null): string;
}