<?php

declare(strict_types=1);

namespace App\Model\User\MessageHandler\Command\User;

use App\Model\User\Domain\ValueObject\Email;
use App\Model\User\Domain\ValueObject\Name;
use App\Model\User\Message\Command\User\ChangeName;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class ChangeNameHandler implements MessageHandlerInterface
{
    private UserRepository $userRepository;
    private EntityManagerInterface $em;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function __invoke(ChangeName $changeName)
    {
        $email = new Email($changeName->email);
        $user = $this->userRepository->getByEmail($email);

        $user->changeFullName(new Name($changeName->firstName, $changeName->lastName));
        $this->em->flush();
    }
}