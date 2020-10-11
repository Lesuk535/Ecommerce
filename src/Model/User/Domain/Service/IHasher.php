<?php

declare(strict_types=1);

namespace App\Model\User\Domain\Service;

interface IHasher
{
    public function hash(string $password): string;

    public function validate(string $password, string $hash): bool;
}