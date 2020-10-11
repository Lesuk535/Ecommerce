<?php

declare(strict_types=1);

namespace App\ReadModel\User;

use App\ReadModel\User\DTO\AuthView;
use App\ReadModel\User\DTO\ShortView;
use App\ReadModel\User\DTO\UserFilter;
use App\ReadModel\User\DTO\UserView;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;
use Knp\Component\Pager\PaginatorInterface;

class UserFetcher
{
    private $connection;
    private $paginator;

    public function __construct(Connection $connection, PaginatorInterface $paginator)
    {
        $this->connection = $connection;
        $this->paginator = $paginator;
    }

    public function findForAuthByEmail(string $email): ?AuthView
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select('*')
            ->from('user_users')
            ->where('email=:email')
            ->setParameter(':email', $email)
            ->execute();

        $stmt->setFetchMode(FetchMode::CUSTOM_OBJECT, AuthView::class);
        $result = $stmt->fetch();

        return $result ?: null;
    }

    public function findForAuthByNetwork(string $oauthType, string $identity): ?AuthView
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select('u.id', 'u.role', 'u.email', 'u.password_hash', 'u.name_first', 'u.name_last', 'u.date')
            ->from('user_users', 'u')
            ->innerJoin('u', 'user_social_networks', 'n', 'n.user_id = u.id')
            ->where('oauth_type = :oauthType AND identity = :identity')
            ->setParameter(':oauthType', $oauthType)
            ->setParameter(':identity', $identity)
            ->execute();

        $stmt->setFetchMode(FetchMode::CUSTOM_OBJECT, AuthView::class);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function findByVerificationToken(string $token): ?ShortView
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select('id, email, role, user_status')
            ->from('user_users', 'u')
            ->leftJoin('u', 'user_tokens', 't', 't.user_id = u.id')
            ->where('t.token = :token')
            ->setParameter(':token', $token)
            ->execute();

        $stmt->setFetchMode(FetchMode::CUSTOM_OBJECT, ShortView::class);

        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function getById(string $id): UserView
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select(
                "id",
                "email",
                "user_status",
                "role",
                "name_first",
                "name_last",
                'TRIM(CONCAT(name_first, \' \', name_last)) AS full_name',
                "avatar"
                )
            ->from('user_users', 'u')
            ->where('u.id = :id')
            ->setParameter(':id', $id)
            ->execute();

        $stmt->setFetchMode(FetchMode::CUSTOM_OBJECT, UserView::class);
        $result = $stmt->fetch();
        $result->networks = $this->getNetworks($id);
        return $result ?: null;
    }

    public function getNetworks(string $id)
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select('oauth_type, identity')
            ->from('user_social_networks', 'n')
            ->where('user_id=:id')
            ->setParameter(':id', $id)
            ->execute();

        return $result = $stmt->fetchAll();
    }

    public function getAll(UserFilter $filter, $page, $limit, $sort, $direction)
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'TRIM(CONCAT(name_first, \' \', name_last)) AS name',
                'date',
                'email',
                'user_status',
                'role',
                )
            ->from('user_users');

        if ($filter->name) {
            $stmt->andWhere($stmt->expr()->like('LOWER(CONCAT(name_first, \' \', name_last))', ':name'));
            $stmt->setParameter(':name', '%' . mb_strtolower($filter->name) . '%');
        }

        if ($filter->email) {
            $stmt->andWhere($stmt->expr()->like('LOWER(email)', ':email'));
            $stmt->setParameter(':email', '%' . mb_strtolower($filter->email) . '%');
        }

        if ($filter->status) {
            $stmt->andWhere($stmt->expr()->like('user_status', ':status'));
            $stmt->setParameter(':status', '%' . $filter->status . '%');
        }

        if ($filter->role) {
            $stmt->andWhere($stmt->expr()->like('role', ':role'));
            $stmt->setParameter(':role', '%' . $filter->role . '%');
        }

        if (!\in_array($sort, ['date', 'name', 'email', 'role', 'user_status'], true)) {
            throw new\UnexpectedValueException('Cannot sotr by ' . $sort);
        }

        $stmt->orderBy($sort, $direction === 'desc' ? 'desc' : 'asc');

        return $this->paginator->paginate($stmt, $page, $limit);
    }

    public function getProfileName(string $id)
    {
        $stmt= $this->connection->createQueryBuilder()
            ->select('TRIM(CONCAT(name_first, \' \', name_last)) AS name',)
            ->from('user_users')
            ->where('id=:id')
            ->setParameter(':id', $id)
            ->execute();

        return $stmt->fetchColumn();
    }

}
