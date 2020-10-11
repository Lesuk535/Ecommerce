<?php

declare(strict_types=1);

namespace App\Model\User\Message\Command\Profile;

use Symfony\Component\Validator\Constraints as Assert;

class EditProfile
{
    public $id;

    /**
     * @Assert\NotBlank()
     */
    public $firstName;

    /**
     * @Assert\NotBlank()
     */
    public $lastName;

    /**
     * @Assert\Email()
     */
    public $newEmail;

    public $email;

    /**
     * @Assert\Image()
     */
    public $avatar;

    public ?string $existingFilename;

    public function isNewEmail()
    {
        return $this->email !== $this->newEmail;
    }

    public function __construct(string $id, ?string $email, ?string $existingFilename)
    {
        $this->id = $id;
        $this->email = $email;
        $this->existingFilename = $existingFilename;
    }
}