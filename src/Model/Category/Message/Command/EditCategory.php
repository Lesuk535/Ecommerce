<?php

declare(strict_types=1);

namespace App\Model\Category\Message\Command;

use Symfony\Component\Validator\Constraints as Assert;

class EditCategory
{
    public string $id;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(min=3)
     */
    public string $name;

    /**
     * @Assert\Image()
     */
    public $image;

    public ?string $existingFilename;

    /**
     * @Assert\Length(min=20)
     */
    public ?string $description;

    public function __construct(string $id, string $name, ?string $description, ?string $existingFilename)
    {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->existingFilename = $existingFilename;
    }
}