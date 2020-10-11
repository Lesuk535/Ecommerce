<?php

declare(strict_types=1);

namespace App\Model\Category\Domain\ValueObject;

use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

class Status
{
    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';

    /**
     * @ORM\Column(type="string", length=16)
     */
    private string $status;

    private array $allStatus;

    public function __construct(string $status)
    {
        Assert::oneOf($status, [
            self::STATUS_ACTIVE,
            self::STATUS_INACTIVE,
        ]);
        $this->status = $status;
    }

    public static function active(): self
    {
        return new self(self::STATUS_ACTIVE);
    }

    public static function inactive(): self
    {
        return new self(self::STATUS_INACTIVE);
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function isInactive(): bool
    {
        return $this->status === self::STATUS_INACTIVE;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function __toString(): string
    {
        return $this->getStatus();
    }
}