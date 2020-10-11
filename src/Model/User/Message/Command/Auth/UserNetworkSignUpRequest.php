<?php

declare(strict_types=1);

namespace App\Model\User\Message\Command\Auth;

class UserNetworkSignUpRequest
{
    /**
     * @Assert\NotBlank()
     */
    public $firstName;

    /**
     * @Assert\NotBlank()
     */
    public $lastName;

    /**
     * @Assert\NotBlank()
     */
    public $oauthType;

    /**
     * @Assert\NotBlank()
     */
    public $identity;

    public $avatar;

    public function __construct($firstName, $lastName, $oauthType, $identity)
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->oauthType = $oauthType;
        $this->identity = $identity;
    }
}