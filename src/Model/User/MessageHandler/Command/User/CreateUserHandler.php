<?php

declare(strict_types=1);

namespace App\Model\User\MessageHandler\Command\User;

use App\Model\User\Factory\UserFactory;
use App\Model\User\Message\Command\User\CreateUser;
use App\Service\Auth\PasswordHasher;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class CreateUserHandler implements MessageHandlerInterface
{
    private UserFactory $userFactory;
    private PasswordHasher $hasher;
    private EntityManagerInterface $em;

    public function __construct(UserFactory $userFactory, PasswordHasher $hasher, EntityManagerInterface $em)
    {
        $this->userFactory = $userFactory;
        $this->hasher = $hasher;
        $this->em = $em;
    }

    public function __invoke(CreateUser $createUser)
    {
        $user = $this->userFactory->create($createUser, $this->hasher);
        $this->em->persist($user);
        $this->em->flush();
    }
}