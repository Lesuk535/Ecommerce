<?php

declare(strict_types=1);

namespace App\Model\User\Domain\Entity;

use App\Model\User\Domain\ValueObject\Email;
use App\Model\User\Domain\ValueObject\Id;
use App\Model\User\Domain\ValueObject\Name;
use App\Model\User\Domain\ValueObject\Role;
use App\Model\User\Domain\ValueObject\Status;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=App\Repository\UserRepository::class)
 * @ORM\Table(name="user_users", uniqueConstraints={@ORM\UniqueConstraint(columns={"email"})})
 */
class User
{
    /**
     * @ORM\Column(type="user_user_id")
     * @ORM\Id
     */
    private Id $id;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private \DateTimeImmutable $date;

    /**
     * @ORM\Column(type="user_user_email", nullable=true)
     */
    private ?Email $email;

    /**
     * @var Email|null
     * @ORM\Column(type="user_user_email", name="new_email", nullable=true)
     */
    private ?Email $newEmail;

    /**
     * @ORM\Embedded(class="App\Model\User\Domain\ValueObject\Name", columnPrefix="name_")
     */
    private Name $name;

    /**
     * @ORM\Column(type="string", name="password_hash", nullable=true)
     */
    private string $password;

    /**
     * @ORM\OneToMany(targetEntity="App\Model\User\Domain\Entity\Token", mappedBy="user", orphanRemoval=true, cascade={"persist"})
     */
    private Collection $tokens;

    /**
     * @ORM\Embedded(class="App\Model\User\Domain\ValueObject\Status", columnPrefix="user_")
     */
    private Status $status;

    /**
     * @ORM\Column(type="user_user_role")
     */
    private Role $role;

    /**
     * @ORM\OneToMany(targetEntity="App\Model\User\Domain\Entity\Network", mappedBy="user", orphanRemoval=true, cascade={"persist"})
     */
    private Collection $networks;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $avatar;

    private function __construct(Id $id, \DateTimeImmutable $date, Name $name)
    {
        $this->id = $id;
        $this->date = $date;
        $this->name = $name;
        $this->tokens = new ArrayCollection();
        $this->networks = new ArrayCollection();
        $this->status = Status::wait();
        $this->role = Role::user();
    }

    public static function create(
        Id $id,
        \DateTimeImmutable $date,
        Name $name,
        Email $email,
        string $passwordHash
    ): self {
        $user = new self($id, $date, $name);
        $user->email = $email;
        $user->password = $passwordHash;
        $user->status = $user->status::active();
        return $user;
    }

    public static function signUpByEmail(
        Id $id,
        \DateTimeImmutable $date,
        Name $name,
        Id $confirmToken,
        \DateTimeImmutable $expiresToken,
        Email $email,
        string $passwordHash
    ): self {
        $user = new self($id, $date, $name);
        $user->email = $email;
        $user->password = $passwordHash;
        $user->status = $user->status::wait();
        $user->tokens->add(Token::email($user, $confirmToken, $expiresToken));
        return $user;
    }

    public function confirmSignUp(string $token, \DateTimeImmutable $date): void
    {
        if (!$this->status->isWait()) {
            throw new \DomainException("Auth is already confirmed.");
        }
        $token = $this->findToken($token);
        if ($token && $token->isExpiredTo($date)) {
            throw new \DomainException('Token is expired.');
        }

        $this->status = $this->status::active();
        $this->tokens->removeElement($token);
    }

    public static function signUpByNetwork(
        Id $id,
        \DateTimeImmutable $date,
        Name $name,
        Id $networkId,
        string $oauthType,
        string $identity,
        string $avatar
    ): self {
        $user = new self($id, $date, $name);
        $user->attachNetwork($networkId, $oauthType, $identity);
        $user->avatar = $avatar;
        $user->status= $user->status::active();
        return $user;
    }

    public function attachNetwork(Id $networkId, string $oauthType, string $identity): void
    {
        foreach ($this->networks as $existing) {
            if ($existing->hasOauthType($oauthType)) {
                throw new \DomainException('Network is already attached.');
            }
        }
        $this->networks->add(new Network($networkId, $oauthType, $this, $identity));
    }

    public function detachNetwork(string $oauthType, string $identity)
    {
        foreach ($this->networks as $existing) {
            if ($existing->isFor($oauthType, $identity)) {
                if (!$this->email && $this->networks->count() === 1) {
                    throw new \DomainException('Unable to detach the last identity.');
                }
                $this->networks->removeElement($existing);
                return;
            }
        }
        throw new \DomainException('Network is not attached.');
    }

    public function resetPassword(
        Id $confirmToken,
        \DateTimeImmutable $expiresToken,
        \DateTimeImmutable $date
    ):void {
        if (!$this->status->isActive()) {
            throw new \DomainException('User is not active.');
        }
        if (!$this->email) {
            throw new \DomainException('Email is not specified');
        }

        if ($this->hasValidToken($date)) {
            throw new \DomainException('Resetting is already requested.');
        }

        $this->tokens->add(Token::resetPassword($this, $confirmToken, $expiresToken));
    }

    public function confirmResetPassword(string $token, \DateTimeImmutable $date, string $hash)
    {
        $token = $this->findToken($token);
        if (!$token) {
            throw new \DomainException('Resetting is not requested.');
        }
        if ($token->isExpiredTo($date)) {
            throw new \DomainException('Reset token is expired.');
        }
        $this->password = $hash;
        $this->tokens->removeElement($token);
    }

    public function changeFullName(Name $name): void
    {
        $this->name = $name;
    }

    public function changeEmail(
        Email $email,
        Id $confirmToken,
        \DateTimeImmutable $expiresToken
    ) {
        if (!$this->status->isActive()) {
            throw new \DomainException("User is not active.");
        }
        if ($this->email && $this->email->isEqual($email))  {
            throw new \DomainException('Email is already same.');
        }
        $this->newEmail = $email;
        $this->tokens->add(Token::changeEmail($this, $confirmToken, $expiresToken));
    }

    public function confirmChangeEmail(string $token, \DateTimeImmutable $date)
    {
        $token = $this->findToken($token);
        if (!$token) {
            throw new \DomainException('Changing is not requested.');
        }

        if ($token->isExpiredTo($date)) {
            throw new \DomainException('Incorrect changing token.');
        }

        if (\is_null($this->newEmail)) {
            throw new \DomainException('Email is already changed');
        }

        $this->email = $this->newEmail;
        $this->newEmail = null;
        $this->tokens->removeElement($token);
    }

    public function changeAvatar(string $path): void
    {
        $this->avatar = $path;
    }

    public function changeRole(Role $role): void
    {
        if ($this->role->isEqual($role)) {
            throw new \DomainException('Role is already same.');
        }
        $this->role = $role;
    }

    public function editUser(Name $name, ?Email $email)
    {
        $this->name = $name;
        $this->email = $email;
    }

    public function ban()
    {
        if ($this->status->isBanned()) {
            throw new \DomainException('User is already banned.');
        }
        $this->status = $this->status::ban();
    }

    public function activate()
    {
        if ($this->status->isActive()) {
            throw new \DomainException('User is already activated.');
        }
        $this->status = $this->status::active();
    }

    private function hasValidToken(\DateTimeImmutable $date): bool
    {
        foreach ($this->tokens as $existing) {
            if (!$existing->isExpiredTo($date)) {
                return true;
            }
        }
        return false;
    }

    private function findToken(string $token): ?Token
    {
        foreach ($this->tokens as $existing) {
            if ($existing->hasToken($token)) {
                return $existing;
            }
        }
        return null;
    }
}