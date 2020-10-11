<?php

declare(strict_types=1);

namespace App\Model\User\MessageHandler\Command\Profile;

use App\Model\User\Domain\Service\IUserRepository;
use App\Model\User\Message\Command\Profile\ChangeEmailConfirm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class ChangeEmailConfirmHandler implements MessageHandlerInterface
{
    private IUserRepository $userRepository;
    private EntityManagerInterface $em;

    public function __construct(IUserRepository $userRepository, EntityManagerInterface $em)
    {
        $this->userRepository = $userRepository;
        $this->em = $em;
    }

    public function __invoke(ChangeEmailConfirm $emailConfirm)
    {
        if (!$user = $this->userRepository->findByVerificationToken($emailConfirm->token)) {
            throw new \DomainException('Incorrect or already confirmed token.');
        }

        $user->confirmChangeEmail($emailConfirm->token, new \DateTimeImmutable());
        $this->em->flush();
    }
}