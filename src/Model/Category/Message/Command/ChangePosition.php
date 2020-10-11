<?php

declare(strict_types=1);

namespace App\Model\Category\Message\Command;

class ChangePosition
{
    public array $positions;

    public function __construct(array $positions)
    {
        $this->positions = $positions;
    }
}