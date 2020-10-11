<?php

declare(strict_types=1);

namespace App\ReadModel\Category\DTO;

class CategoryView
{
    public $id;
    public $image;
    public $name;
    public $status;
    public $description;
    public $order;
    public $parent;
    public $subcategory_count;
    public $attachment_category_count;

    public function getImagePath()
    {
        if ($this->image !== null) {
            return '/category/' . $this->image;
        }
        return null;
    }
}