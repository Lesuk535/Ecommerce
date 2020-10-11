<?php

declare(strict_types=1);

namespace App\Model\User\Message\Command\Auth;

use Symfony\Component\Validator\Constraints as Assert;

class ResetPasswordRequest
{
    /**
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    public $email;
}