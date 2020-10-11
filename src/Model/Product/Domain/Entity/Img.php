<?php

declare(strict_types=1);

namespace App\Model\Product\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="product_images")
 */
class Img
{
    /**
     * @ORM\Column(type="product_id")
     * @ORM\Id
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="App\Model\Product\Domain\Entity\Product", inversedBy="images")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     */
    private $product;
}