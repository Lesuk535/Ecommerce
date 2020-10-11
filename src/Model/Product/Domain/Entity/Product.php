<?php

declare(strict_types=1);

namespace App\Model\Product\Domain\Entity;

use App\Model\Product\Domain\ValueObject\Id;
use App\Model\Product\Domain\ValueObject\Price;
use App\Model\Product\Domain\ValueObject\Status;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\JoinTable;

/**
 * @ORM\Entity(repositoryClass=App\Repository\ProductRepository::class)
 * @ORM\Table(name="product_products")
 */
class Product
{
    /**
     * @ORM\Column(type="product_id")
     * @ORM\Id
     */
    private Id $id;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private \DateTimeImmutable $createdAt;

    /**
     * @ORM\Column(type="string")
     */
    private string $name;

    /**
     * @ORM\Column(type="product_status")
     */
    private Status $status;

    /**
     * @ORM\Column(type="text")
     */
    private string $description;

    /**
     * @ORM\Embedded(class="App\Model\Product\Domain\ValueObject\Price", columnPrefix = false)
     */
    private Price $price;

    /**
     * @ORM\Column(type="integer");
     */
    private $quantity = 0;

    /**
     * @ORM\Column(type="json_array", nullable=true, options={"jsonb"=true})
     */
    private $attributes;

    /**
     * @ORM\OneToMany(targetEntity="App\Model\Product\Domain\Entity\Img", mappedBy="product")
     */
    private Collection $images;

    /**
     * @ORM\ManyToMany(targetEntity="App\Model\Category\Domain\Entity\Category")
     * @JoinTable(name="product_products_categories",
     *     joinColumns={@JoinColumn(name="product_id", referencedColumnName="id")},
     *     inverseJoinColumns={@JoinColumn(name="category_id", referencedColumnName="id")}
     *     )
     */
    private Collection $categories;

    /**
     * @ORM\ManyToOne(targetEntity="App\Model\Brand\Domain\Entity\Brand")
     * @JoinColumn(name="brand_id", referencedColumnName="id")
     */
    private Collection $brand;

    public function __construct(Id $id, \DateTimeImmutable $date, string $name)
    {
        $this->id = $id;
        $this->date = $date;
        $this->name = $name;
        $this->images = new ArrayCollection();
        $this->categories = new ArrayCollection();
        $this->brand = new ArrayCollection();
    }

    public static function create(
        Id $id,
        \DateTimeImmutable $date,
        string $name,
        string $description,
        int $quantity,
        int $price,
        ?int $discount
    ): self {
        $product = new self($id, $date, $name);
        $product->description = $description;
        $product->quantity = $quantity;
        $product->price = new Price($price, $discount);
    }

}
