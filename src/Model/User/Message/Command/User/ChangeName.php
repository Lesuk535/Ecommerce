<?php

declare(strict_types=1);

namespace App\Model\User\Message\Command\User;

class ChangeName
{
    /**
     * @Assert\NotBlank()
     */
    public string $firstName;
    /**
     * @Assert\NotBlank()
     */
    public string $lastName;
    public string $email;

    public function __construct(string $firstName, string $lastName, string $email)
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
    }


}