<?php

declare(strict_types=1);

namespace App\Service\Auth;

use App\Model\User\Domain\Service\ITokenizer;
use App\Model\User\Domain\ValueObject\Id;
use App\Model\User\DTO\TokenDTO;

final class VerificationTokenizer implements ITokenizer
{
    private $interval;

    public function __construct(\DateInterval $interval)
    {
        $this->interval = $interval;
    }

    public function generate(): TokenDTO
    {
        return new TokenDTO(
            Id::next(),
            (new \DateTimeImmutable())->add($this->interval)
        );
    }
}