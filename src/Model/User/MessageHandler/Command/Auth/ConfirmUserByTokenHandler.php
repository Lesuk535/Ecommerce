<?php

declare(strict_types=1);

namespace App\Model\User\MessageHandler\Command\Auth;

use App\Model\User\Domain\Service\IUserRepository;
use App\Model\User\Message\Command\Auth\ConfirmingUserByToken;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class ConfirmUserByTokenHandler implements MessageHandlerInterface
{
    private IUserRepository $userRepository;
    private EntityManagerInterface $em;

    public function __construct(IUserRepository $userRepository, EntityManagerInterface $em)
    {
        $this->userRepository = $userRepository;
        $this->em = $em;
    }

    public function __invoke(ConfirmingUserByToken $confirmingUser)
    {
        if (!$user = $this->userRepository->findByVerificationToken($confirmingUser->token)) {
            throw new \DomainException('Incorrect or already confirmed token.');
        }

        $user->confirmSignUp($confirmingUser->token, new \DateTimeImmutable);
        $this->em->flush();
    }
}