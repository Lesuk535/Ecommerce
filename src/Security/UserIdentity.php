<?php

declare(strict_types=1);

namespace App\Security;

use Symfony\Component\Security\Core\User\UserInterface;

class UserIdentity implements UserInterface
{
    private string $id;

    private string $role;

    private ?string $password;

    private string $username;

    public function __construct(string $id, string $role, ?string $password, string $username)
    {
        $this->id = $id;
        $this->role = $role;
        $this->password = $password;
        $this->username = $username;
    }

    public function getId(): string
    {
        return $this->id;
    }
    public function getRoles(): array
    {
        return [$this->role];
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getSalt()
    {
        return null;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function eraseCredentials(): void
    {
    }
}