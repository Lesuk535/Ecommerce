<?php

declare(strict_types=1);

namespace App\Model\User\MessageHandler\Command\Auth;

use App\Model\User\Domain\Service\IHasher;
use App\Model\User\Domain\Service\ITokenizer;
use App\Model\User\Domain\Service\TokenSender;
use App\Model\User\Domain\ValueObject\Email;
use App\Model\User\Message\Command\Auth\UserSignUpRequest;
use App\Model\User\Factory\UserFactory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class UserSignUpRequestHandler implements MessageHandlerInterface
{
    private TokenSender $verificationSender;
    private ITokenizer $verificationTokenizer;
    private UserFactory $userFactory;
    private IHasher $passwordHasher;
    private EntityManagerInterface $em;
    private string $emailTemplate;

    public function __construct(
        TokenSender $verificationSender,
        ITokenizer $verificationTokenizer,
        UserFactory $userFactory,
        IHasher $passwordHasher,
        EntityManagerInterface $em,
        string $signUpConfirmTemlate
    ) {
        $this->verificationSender = $verificationSender;
        $this->verificationTokenizer = $verificationTokenizer;
        $this->userFactory = $userFactory;
        $this->passwordHasher = $passwordHasher;
        $this->em = $em;
        $this->emailTemplate = $signUpConfirmTemlate;
    }

    public function __invoke(UserSignUpRequest $signUpRequest): void
    {
        $tokenDTO = $this->verificationTokenizer->generate();
        $user = $this->userFactory->createSignUp($signUpRequest, $this->passwordHasher, $tokenDTO);

        $this->verificationSender->send(
            new Email($signUpRequest->email),
            $tokenDTO->token,
            $this->emailTemplate
        );

        $this->em->persist($user);
        $this->em->flush();
    }
}