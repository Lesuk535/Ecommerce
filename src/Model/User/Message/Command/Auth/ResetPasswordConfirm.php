<?php

declare(strict_types=1);

namespace App\Model\User\Message\Command\Auth;

use Symfony\Component\Validator\Constraints as Assert;

class ResetPasswordConfirm
{
    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Length(min=6)
     */
    public $password;

    public string $token;
}