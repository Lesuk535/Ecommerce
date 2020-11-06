<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\Category\Domain\Entity\Category;
use App\Model\Category\Domain\Service\ICategoryRepository;
use App\Model\EntityNotFoundException;
use App\Model\Category\Domain\ValueObject\Id;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CategoryRepository extends ServiceEntityRepository implements ICategoryRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    public function getByIds(array $ids): array
    {
        $qb = $this->createQueryBuilder('c');
        return $qb->select('c')
            ->andWhere($qb->expr()->in('c.id', ':id'))
            ->setParameter(':id', $ids)
            ->getQuery()->getResult();
    }

    public function getById(Id $id): Category
    {
        /** @var Category $category */
        if (!$category = $this->findOneBy(['id' => $id->getValue()])) {
            throw new EntityNotFoundException('Category is not found');
        };
        return $category;
    }

    public function getMaxOrder(): int
    {
        $qb = $this->createQueryBuilder('c');
        $result =  $qb->select($qb->expr()->max('c.categoryOrder'))
            ->getQuery()->getSingleScalarResult();

        return (int) $result;
    }
}