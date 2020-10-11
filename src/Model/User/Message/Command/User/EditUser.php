<?php

declare(strict_types=1);

namespace App\Model\User\Message\Command\User;

use Symfony\Component\Validator\Constraints as Assert;

class EditUser
{
    public $id;

    public $profileId;
    /**
     * @Assert\NotBlank()
     */
    public $firstName;

    /**
     * @Assert\NotBlank()
     */
    public $lastName;

    public $email;

    /**
     * @Assert\Email()
     */
    public $newEmail;

    /**
     * @Assert\Image()
     */
    public $avatar;

    public $role;

    /**
     * @Assert\NotBlank()
     */
    public $newRole;

    public ?string $existingFilename;

    public function isNewEmail()
    {
        return $this->email !== $this->newEmail;
    }

    public function __construct(string $id, string $profileId, ?string $email, string $role, ?string $existingFilename)
    {
        $this->id = $id;
        $this->profileId = $profileId;
        $this->email = $email;
        $this->role = $role;
        $this->existingFilename = $existingFilename;
    }
}