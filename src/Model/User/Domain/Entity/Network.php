<?php

declare(strict_types=1);

namespace App\Model\User\Domain\Entity;

use App\Model\User\Domain\ValueObject\Email;
use App\Model\User\Domain\ValueObject\Id;
use App\Model\User\Domain\ValueObject\Name;
use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

/**
 * @ORM\Entity()
 * @ORM\Table(name="user_social_networks")
 */
class Network
{
    private const FACEBOOK = 'facebook';
    private const GOOGLE = 'google';
    /**
     * @ORM\Column(type="user_user_id")
     * @ORM\Id
     */
    private Id $id;

    /**
     * @ORM\Column(type="string", length=32, nullable=true)
     */
    private string $oauthType;

    /**
     * @ORM\ManyToOne(targetEntity="App\Model\User\Domain\Entity\User", inversedBy="networks")
     * @ORM\JoinColumn("user_id", referencedColumnName="id", nullable=false)
     */
    private User $user;

    /**
     * @ORM\Column(type="string", length=32, nullable=true)
     */
    private $identity;

    /**
     * Network constructor.
     * @param Id $id
     * @param string $oauthType
     * @param User $user
     * @param $identity
     */
    public function __construct(Id $id, string $oauthType, User $user, $identity)
    {
        Assert::oneOf($oauthType, [
            self::FACEBOOK,
            self::GOOGLE
        ]);

        $this->id = $id;
        $this->oauthType = $oauthType;
        $this->user = $user;
        $this->identity = $identity;
    }

    public function hasOauthType(string $oauthType): bool
    {
        return $this->oauthType === $oauthType;
    }

    public function isFor(string $oauthType, string $identity): bool
    {
        return $this->oauthType === $oauthType && $this->identity === $identity;
    }

}