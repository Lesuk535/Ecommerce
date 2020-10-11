<?php

declare(strict_types=1);

namespace App\Model\User\Message\Command\Profile;

class ChangeEmailConfirm
{
    public $token;

    public function __construct(string $token)
    {
        $this->token = $token;
    }
}