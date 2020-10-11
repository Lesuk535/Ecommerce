<?php

declare(strict_types=1);

namespace App\Security;

use App\ReadModel\User\DTO\AuthView;
use App\ReadModel\User\UserFetcher;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface
{
    private $fetcher;

    public function __construct(UserFetcher $fetcher)
    {
        $this->fetcher = $fetcher;
    }

    public function loadUserByUsername(string $username): UserInterface
    {
        $user = $this->loadUser($username);
        return $this->identityByUser($user, $username);
    }

    public function refreshUser(UserInterface $identity)
    {
        if (!$identity instanceof UserIdentity) {
            throw new UnsupportedUserException(sprintf('Invalid user class "%s".', get_class($identity)));
        }

        $user = $this->loadUser($identity->getUsername());
        return $this->identityByUser($user, $identity->getUsername());
    }

    public function supportsClass(string $class): bool
    {
        return $class === UserIdentity::class;
    }

    private function loadUser($username): AuthView
    {
        $chunks = explode(':', $username);

        if (\count($chunks) === 2 && $user = $this->fetcher->findForAuthByNetwork($chunks[0], $chunks[1])) {
            return $user;
        }

        if ($user = $this->fetcher->findForAuthByEmail($username)) {
            return $user;
        }

        throw new UsernameNotFoundException('');
    }

    private function identityByUser(AuthView $user, string $username): UserIdentity
    {
        return new UserIdentity(
            $user->id,
            $user->role,
            $user->password_hash ?: '',
            $user->email ?: $username
        );
    }
}