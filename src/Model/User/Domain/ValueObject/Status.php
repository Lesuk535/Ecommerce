<?php

declare(strict_types=1);

namespace App\Model\User\Domain\ValueObject;

use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

/**
 * @ORM\Embeddable
 */
class Status
{
    public const STATUS_ACTIVE = 'active';
    public const STATUS_WAIT = 'wait';
    public const STATUS_NEW = 'new';
    public const STATUS_BAN = 'ban';

    /**
     * @ORM\Column(type="string", length=16)
     */
    private string $status;

    private function __construct(string $status)
    {
        Assert::oneOf($status, [
            self::STATUS_NEW,
            self::STATUS_ACTIVE,
            self::STATUS_WAIT,
            self::STATUS_BAN
        ]);
        $this->status = $status;
    }

    public static function new(): self
    {
        return new self(self::STATUS_NEW);
    }

    public static function wait(): self
    {
        return new self(self::STATUS_WAIT);
    }

    public static function active(): self
    {
        return new self(self::STATUS_ACTIVE);
    }

    public static function ban(): self
    {
        return new self(self::STATUS_BAN);
    }

    public function isWait(): bool
    {
        return $this->status === self::STATUS_WAIT;
    }

    public function isNew(): bool
    {
        return $this->status === self::STATUS_NEW;
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function isBanned(): bool
    {
        return $this->status === self::STATUS_BAN;
    }
}