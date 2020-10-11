<?php

declare(strict_types=1);

namespace App\Model\Category\Message\Command;

class AttachSubcategories
{
    public string $id;
    public array $children = [];

    public function __construct(string $id)
    {
        $this->id = $id;
    }
}