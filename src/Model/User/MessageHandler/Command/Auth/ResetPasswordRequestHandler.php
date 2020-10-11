<?php

declare(strict_types=1);

namespace App\Model\User\MessageHandler\Command\Auth;

use App\Model\User\Domain\Service\ITokenizer;
use App\Model\User\Domain\Service\IUserRepository;
use App\Model\User\Domain\Service\TokenSender;
use App\Model\User\Domain\ValueObject\Email;
use App\Model\User\Message\Command\Auth\ResetPasswordRequest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class ResetPasswordRequestHandler implements MessageHandlerInterface
{
    private IUserRepository $userRepository;
    private ITokenizer $verificationTokenizer;
    private TokenSender $verificationSender;
    private EntityManagerInterface $em;
    private string $emailTemplate;

    public function __construct(
        IUserRepository $userRepository,
        ITokenizer $verificationTokenizer,
        TokenSender $verificationSender,
        EntityManagerInterface $em,
        string $resetPasswordEmailTemplate
    ) {
        $this->userRepository = $userRepository;
        $this->verificationTokenizer = $verificationTokenizer;
        $this->verificationSender = $verificationSender;
        $this->em = $em;
        $this->emailTemplate = $resetPasswordEmailTemplate;
    }

    public function __invoke(ResetPasswordRequest $resetPasswordRequest)
    {
        $email = new Email($resetPasswordRequest->email);
        $user = $this->userRepository->findForResetPassword($email);
        $tokenDto = $this->verificationTokenizer->generate();

        $user->resetPassword($tokenDto->token, $tokenDto->expiresAt, new \DateTimeImmutable());
        $this->verificationSender->send($email, $tokenDto->token, $this->emailTemplate);
        $this->em->flush();
    }
}