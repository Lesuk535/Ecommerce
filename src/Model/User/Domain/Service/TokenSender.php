<?php

declare(strict_types=1);

namespace App\Model\User\Domain\Service;

use App\Model\User\Domain\Entity\Token;
use App\Model\User\Domain\ValueObject\Email;
use App\Model\User\Domain\ValueObject\Id;

interface TokenSender
{
    public function send(Email $email, Id $token, string $templatePath): void ;
}