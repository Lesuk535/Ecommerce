<?php

declare(strict_types=1);

namespace App\Model\Category\Domain\Service;

use App\Model\Category\Domain\Entity\Category;
use App\Model\Category\Domain\ValueObject\Id;

interface ICategoryRepository
{
    public function getByIds(array $ids): array;
    public function getById(Id $id): Category;
    public function getMaxOrder(): int;
}