<?php

declare(strict_types=1);

namespace App\Model\User\Message\Command\Auth;

class ConfirmingUserByToken
{
    public string $token;

    public function __construct(string $token)
    {
        $this->token = $token;
    }
}