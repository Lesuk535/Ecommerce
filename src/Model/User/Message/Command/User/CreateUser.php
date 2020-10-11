<?php

declare(strict_types=1);

namespace App\Model\User\Message\Command\User;

use Symfony\Component\Validator\Constraints as Assert;

class CreateUser
{
    /**
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    public $email;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(min=6)
     */
    public $password;

    /**
     * @Assert\NotBlank()
     */
    public $firstName;

    /**
     * @Assert\NotBlank()
     */
    public $lastName;
}