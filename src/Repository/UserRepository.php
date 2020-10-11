<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\User\Domain\Entity\User;
use App\Model\User\Domain\Service\IUserRepository;
use App\Model\User\Domain\ValueObject\Email;
use App\Model\User\Domain\ValueObject\Id;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use App\Model\EntityNotFoundException;
use Doctrine\Persistence\ManagerRegistry;

class UserRepository extends ServiceEntityRepository implements IUserRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function hasByEmail(Email $email): bool
    {
        return $this->createQueryBuilder('u')
                ->select('COUNT(u.id)')
                ->andWhere('u.email = :email')
                ->setParameter(':email', $email->getValue())
                ->getQuery()->getSingleScalarResult() > 0;
    }

    public function hasByNetworkIdentity(string $oauthType, string $identity): bool
    {
        return $this->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->innerJoin('u.networks', 'n')
            ->andWhere('n.oauthType = :oauthType and n.identity = :identity')
            ->setParameter(':oauthType', $oauthType)
            ->setParameter('identity', $identity)
            ->getQuery()->getSingleScalarResult() > 0;
    }

    public function findForResetPassword(Email $email): ?User
    {
         return $this->createQueryBuilder('u')
             ->select('u, t')
             ->innerJoin('u.tokens', 't')
             ->andWhere('u.email = :email')
             ->setParameter(':email', $email->getValue())
             ->getQuery()->getOneOrNullResult();
    }

    public function findByVerificationToken(string $token): ?User
    {
        return $this->createQueryBuilder('u')
            ->innerJoin('u.tokens', 't')
            ->andWhere('t.token = :token')
            ->setParameter(":token", $token)
            ->getQuery()->getOneOrNullResult();
    }

    public function findSocialNetworkByEmail(Email $email): ?User
    {
        return $this->createQueryBuilder('u')
            ->innerJoin('u.networks', 'n')
            ->andWhere()
            ->getQuery()->getOneOrNullResult();
    }

    public function getByEmail(Email $email): User
    {
        /** @var User $user */
        if (!$user = $this->findOneBy(['email' => $email->getValue()])) {
            throw new EntityNotFoundException('User is not found');
        };
        return $user;
    }

    public function getById(Id $id): User
    {
        /** @var User $user */
        if (!$user = $this->findOneBy(['id' => $id->getValue()])) {
            throw new EntityNotFoundException('User is not found');
        };
        return $user;
    }
}
