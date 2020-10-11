<?php

declare(strict_types=1);

namespace App\ReadModel\User\DTO;

class AuthView
{
    public $id;
    public $role;
    public $password_hash;
    public $email;
    public $name_first;
    public $name_last;
    public $avatar;
    public $user_status;
    public $oauth_type;

    public function getAvatarPath()
    {
        if ($this->avatar !== null) {
            return '/user_avatar/' . $this->avatar;
        }
        return null;
    }
}