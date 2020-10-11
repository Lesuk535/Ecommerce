<?php

declare(strict_types=1);

namespace App\Service\Auth;

use App\Model\User\Domain\Service\IHasher;

final class PasswordHasher implements IHasher
{
    public function hash(string $password): string
    {
        $hash = password_hash($password, PASSWORD_ARGON2ID);

        if ($hash === false) {
            throw new \RuntimeException('Unable to generate hash.');
        }

        return $hash;
    }

    public function validate(string $password, string $hash): bool
    {
        return \password_verify($password, $hash);
    }
}