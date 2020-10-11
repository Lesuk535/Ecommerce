<?php

declare(strict_types=1);

namespace App\Model\User\DTO;

use App\Model\User\Domain\ValueObject\Id;

class TokenDTO
{
    public Id $token;

    public \DateTimeImmutable $expiresAt;

    public function __construct(Id $token, \DateTimeImmutable $expiresAt)
    {
        $this->token = $token;
        $this->expiresAt = $expiresAt;
    }
}