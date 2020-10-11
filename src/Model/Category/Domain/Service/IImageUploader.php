<?php

declare(strict_types=1);

namespace App\Model\Category\Domain\Service;

interface IImageUploader
{
    public function upload($file, ?string $existingFilename = null): string;
}