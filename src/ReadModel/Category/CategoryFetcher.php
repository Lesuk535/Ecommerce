<?php

declare(strict_types=1);

namespace App\ReadModel\Category;

use App\Model\Category\Domain\ValueObject\Id;
use App\ReadModel\Category\DTO\CategoryView;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;

class CategoryFetcher
{
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function getCategoriesWithoutParent(): ?array
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select('id', 'name')
            ->from('category')
            ->where('parent_id IS null')
            ->execute();
        $result = $stmt->fetchAll(\PDO::FETCH_KEY_PAIR);
        return $result;
    }

    public function getAll(): ?array
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'image',
                'name',
                'status',
                'category_order AS order'
            )
            ->from('category')
            ->execute();

        return $stmt->fetchAll(FetchMode::CUSTOM_OBJECT, CategoryView::class);
    }

    public function findAllByParent(string $id): ?array
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'image',
                'name',
                'status',
                'category_order AS order'
            )
            ->from('category', 'c')
            ->where('c.parent_id = :id')
            ->orderBy('c.category_order', 'ASC')
            ->setParameter(':id', $id)
            ->execute();

        return $stmt->fetchAll(FetchMode::CUSTOM_OBJECT, CategoryView::class);
    }

    public function findAllByEmptyParent()
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'image',
                'name',
                'status',
                'category_order AS order'
            )
            ->from('category', 'c')
            ->where('parent_id IS null')
            ->orderBy('c.category_order', 'ASC')
            ->execute();
        $result = $stmt->fetchAll(FetchMode::CUSTOM_OBJECT, CategoryView::class);
        return $result;
    }

    public function getParentById(string $id): ?CategoryView
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'image',
                'name',
                'status',
            )
            ->from('category', 'c')
            ->where('c.id = :id')
            ->setParameter(':id', $id)
            ->execute();

        return $stmt->fetch(FetchMode::CUSTOM_OBJECT, CategoryView::class);
    }

    public function findParentId(string $id)
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select(
                'parent_id'
                )
            ->from('category', 'c')
            ->where('c.id = :id')
            ->setParameter(':id', $id)
            ->execute();

        return $stmt->fetchColumn();
    }

    public function getById(string $id): ?CategoryView
    {
        $subcategoryCount = $this->connection->createQueryBuilder()
            ->select("COUNT(parent_id) as subcategoryCount")
            ->from('category', 'c')
            ->where('c.parent_id=:id')
            ->setParameter(':id', $id)
            ->getSQL();

        $AttachmentCategoryCount = $this->connection->createQueryBuilder()
            ->select("COUNT(id)")
            ->from('category')
            ->where('parent_id IS null')
            ->andWhere('c.id != :id')
            ->getSQL();

        $stmt = $this->connection->createQueryBuilder()
            ->select(
                "id",
                "image",
                "name",
                "description",
                "status",
                "parent_id AS parent",
                sprintf("(%s) AS subcategory_count", $subcategoryCount),
                sprintf("(%s) AS attachment_category_count", $AttachmentCategoryCount),
                "category_order AS order"
            )
            ->from('category', 'c')
            ->where('c.id = :id')
            ->setParameter(':id', $id)
            ->execute();

        $stmt->setFetchMode(FetchMode::CUSTOM_OBJECT, CategoryView::class);
        $result = $stmt->fetch();
        return $result ?: null;
    }

}