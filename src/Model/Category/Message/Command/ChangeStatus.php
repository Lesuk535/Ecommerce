<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use Symfony\Component\Validator\Constraints as Assert;

class ChangeStatus
{
    /**
     * @Assert\NotBlank()
     */
    public string $id;

    /**
     * @Assert\NotBlank()
     */
    public string $status;

    public function __construct(string $id, string $status)
    {
        $this->id = $id;
        $this->status = $status;
    }
}