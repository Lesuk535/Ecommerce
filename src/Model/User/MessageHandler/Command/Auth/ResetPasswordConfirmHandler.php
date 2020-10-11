<?php

declare(strict_types=1);

namespace App\Model\User\MessageHandler\Command\Auth;

use App\Model\User\Domain\Service\IHasher;
use App\Model\User\Domain\Service\IUserRepository;
use App\Model\User\Message\Command\Auth\ResetPasswordConfirm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class ResetPasswordConfirmHandler implements MessageHandlerInterface
{
    private IUserRepository $userRepository;
    private IHasher $passwordHasher;
    private EntityManagerInterface $em;

    public function __construct(IUserRepository $userRepository, IHasher $passwordHasher, EntityManagerInterface $em)
    {
        $this->userRepository = $userRepository;
        $this->passwordHasher = $passwordHasher;
        $this->em = $em;
    }

    public function __invoke(ResetPasswordConfirm $resetPasswordConfirm)
    {
        if (!$user = $this->userRepository->findByVerificationToken($resetPasswordConfirm->token)) {
            throw new \DomainException('Incorrect or already confirmed token.');
        }

        $user->confirmResetPassword(
            $resetPasswordConfirm->token,
            new \DateTimeImmutable(),
            $this->passwordHasher->hash($resetPasswordConfirm->password)
        );
        $this->em->flush();
    }
}