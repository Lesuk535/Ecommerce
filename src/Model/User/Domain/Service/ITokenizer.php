<?php

declare(strict_types=1);

namespace App\Model\User\Domain\Service;

use App\Model\User\DTO\TokenDTO;

Interface ITokenizer
{
    public function generate(): TokenDTO;
}