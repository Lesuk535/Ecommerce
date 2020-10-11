<?php

declare(strict_types=1);

namespace App\Model\User\Domain\ValueObject;

use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

/**
 * @ORM\Embeddable
 */
class Name
{
    /**
     * @ORM\Column(type="string")
     */
    private string $first;

    /**
     * @ORM\Column(type="string")
     */
    private string $last;

    public function __construct(string $first, string $last)
    {
        Assert::notEmpty($first);
        Assert::notEmpty($last);

        $this->first = $first;
        $this->last = $last;
    }
}