<?php

declare(strict_types=1);

namespace App\Model\Category\Message\Command;

use Symfony\Component\Validator\Constraints as Assert;

class CreateCategory
{
    /**
     * @Assert\NotBlank()
     * @Assert\Length(min=3)
     */
    public $name;

    /**
     * @Assert\Image()
     */
    public $image;

    public $children;
    /**
     * @Assert\Length(min=20)
     */
    public $description;
}