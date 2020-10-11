<?php

declare(strict_types=1);

namespace App\Model\User\Domain\Service;

use App\Model\User\Domain\Entity\User;
use App\Model\User\Domain\ValueObject\Email;
use App\Model\User\Domain\ValueObject\Id;

interface IUserRepository
{
    public function hasByEmail(Email $email): bool;

    public function findByVerificationToken(string $token): ?User;

    public function getByEmail(Email $email): User;

    public function getById(Id $id): User;

    public function findSocialNetworkByEmail(Email $email): ?User;

    public function hasByNetworkIdentity(string $oauthType, string $identity): bool;

    public function findForResetPassword(Email $email): ?User;
}
