<?php

declare(strict_types=1);

namespace App\Model\User\Domain\ValueObject;

use Webmozart\Assert\Assert;

class Email
{
    private $email;

    public function __construct(string $value)
    {
        $this->email = $this->emailValidate($value);
    }

    public function getValue()
    {
        return $this->email;
    }

    public function isEqual(self $email): bool
    {
        return $email->getValue() === $this->email;
    }
    private function emailValidate(string $value): string
    {
        Assert::notEmpty($value);
        if (!\filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('incorrect email.');
        }
        return \mb_strtolower($value);
    }
}
