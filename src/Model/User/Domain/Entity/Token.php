<?php

declare(strict_types=1);

namespace App\Model\User\Domain\Entity;

use App\Model\User\Domain\ValueObject\Id;
use Webmozart\Assert\Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="user_tokens")
 */
class Token
{
    private const TOKEN_EMAIL = 'email';
    private const TOKEN_RESET_PASSWORD = 'reset_password';
    private const TOKEN_CHANGE_EMAIL = 'change_email';
    /**
     * @ORM\Column(type="user_user_id")
     * @ORM\Id
     */
    private Id $token;
    /**
     * @ORM\Column(type="string", length=180, nullable=false)
     */
    private string $name;
    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private \DateTimeImmutable $expiresAt;
    /**
     * @ORM\ManyToOne(targetEntity="App\Model\User\Domain\Entity\User", inversedBy="tokens")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    private $user;

    private function __construct(User $user, Id $token, \DateTimeImmutable $expiresAt)
    {
        $this->token = $token;
        $this->user = $user;
        $this->expiresAt = $expiresAt;
    }

    public static function email(User $user, Id $token, \DateTimeImmutable $expiresAt)
    {
        $token = new self($user, $token, $expiresAt);
        $token->name = $token->nameValidate(self::TOKEN_EMAIL);
        return $token;
    }

    public static function resetPassword(User $user, Id $token, \DateTimeImmutable $expiresAt)
    {
        $token = new self($user, $token, $expiresAt);
        $token->name = $token->nameValidate(self::TOKEN_RESET_PASSWORD);
        return $token;
    }

    public static function changeEmail(User $user, Id $token, \DateTimeImmutable $expiresAt)
    {
        $token = new self($user, $token, $expiresAt);
        $token->name = $token->nameValidate(self::TOKEN_CHANGE_EMAIL);
        return $token;
    }

    public function isExpiredTo(\DateTimeImmutable $date): bool
    {
        return $this->expiresAt <= $date;
    }

    public function isEmpty(): bool
    {
        return empty($this->token);
    }

    public function hasToken(string $token): bool
    {
        return $this->token->getValue() === $token;
    }

    private function nameValidate(string $name)
    {
        Assert::notEmpty($name);
        return \mb_strtolower($name);
    }
}