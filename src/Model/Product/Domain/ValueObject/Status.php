<?php

declare(strict_types=1);

namespace App\Model\Product\Domain\ValueObject;

use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

class Status
{
    public const STATUS_UNAVAILABLE = 'unavailable';
    public const STATUS_AVAILABLE = 'available';
    public const STATUS_NEW = 'new';

    /**
     * @ORM\Column(type="string", length=16)
     */
    private string $status;

    public function __construct(string $status)
    {
        Assert::oneOf($status, [
            self::STATUS_UNAVAILABLE,
            self::STATUS_AVAILABLE,
        ]);
        $this->status = $status;
    }

    public static function unavailable(): self
    {
        return new self(self::STATUS_UNAVAILABLE);
    }

    public static function available(): self
    {
        return new self(self::STATUS_AVAILABLE);
    }

    public function isUnavailable(): bool
    {
        return $this->status === self::STATUS_UNAVAILABLE;
    }

    public function isAvailable(): bool
    {
        return $this->status === self::STATUS_AVAILABLE;
    }
}