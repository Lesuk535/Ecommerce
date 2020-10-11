<?php

declare(strict_types=1);

namespace App\Model\Product\Domain\ValueObject;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable
 */
class Price
{
    /**
     * @ORM\Column(type="bigint")
     */
    private $price;

    /**
     * @ORM\Column(type="integer", nullable=true);
     */
    private $percent_discount;

    public function __construct(int $price, int $percent_discount = null)
    {
        $this->price = $price;
        $this->percent_discount = $percent_discount;
    }
}