<?php

declare(strict_types=1);

namespace App\Model\User\Message\Command\Profile;

class AttachNetwork
{
    public string $userId;
    public string $oauthType;
    public string $identity;

    public function __construct(string $userId, string $oauthType, string $identity)
    {
        $this->userId = $userId;
        $this->oauthType = $oauthType;
        $this->identity = $identity;
    }
}