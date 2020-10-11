<?php

declare(strict_types=1);

namespace App\ReadModel\User\DTO;

class UserView
{
    public $id;
    public $role;
    public $email;
    public $name_first;
    public $name_last;
    public $full_name;
    public $avatar;
    public $user_status;
    public $networks;

    public function getAvatarPath()
    {
        if ($this->avatar !== null) {
            return '/user_avatar/' . $this->avatar;
        }
        return null;
    }
}