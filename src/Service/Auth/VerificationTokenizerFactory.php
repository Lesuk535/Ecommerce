<?php

declare(strict_types=1);

namespace App\Service\Auth;

final class VerificationTokenizerFactory
{
    public function create(string $interval): VerificationTokenizer
    {
        return new VerificationTokenizer(new \DateInterval($interval));
    }
}