<?php

declare(strict_types=1);

namespace App\Model\User\MessageHandler\Command\Profile;

use App\Model\User\Domain\Service\IAvatarUploader;
use App\Model\User\Domain\Service\ITokenizer;
use App\Model\User\Domain\Service\IUserRepository;
use App\Model\User\Domain\Service\TokenSender;
use App\Model\User\Domain\ValueObject\Email;
use App\Model\User\Domain\ValueObject\Id;
use App\Model\User\Domain\ValueObject\Name;
use App\Model\User\Message\Command\Profile\EditProfile;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class EditProfileHandler implements MessageHandlerInterface
{
    private IAvatarUploader $uploader;
    private IUserRepository $userRepository;
    private EntityManagerInterface $em;
    private ITokenizer $verificationTokenizer;
    private TokenSender $verificationSender;
    private string $emailTemplate;

    public function __construct(
        IAvatarUploader $avatarUploader,
        IUserRepository $userRepository,
        EntityManagerInterface $em,
        ITokenizer $verificationTokenizer,
        TokenSender $verificationSender,
        string $changeEmailConfirmTemplate
    ) {
        $this->uploader = $avatarUploader;
        $this->userRepository = $userRepository;
        $this->em = $em;
        $this->verificationTokenizer = $verificationTokenizer;
        $this->verificationSender = $verificationSender;
        $this->emailTemplate = $changeEmailConfirmTemplate;
    }

    public function __invoke(EditProfile $editProfile)
    {
        $user = $this->userRepository->getById(new Id($editProfile->id));

        if ($editProfile->avatar !== null) {
            $uploadedFile = $this->uploader->uploadUserAvatar($editProfile->avatar, $editProfile->existingFilename);
            $user->changeAvatar($uploadedFile);
        }

        $user->changeFullName(new Name($editProfile->firstName, $editProfile->lastName));

        if ($editProfile->isNewEmail() && $this->userRepository->hasByEmail(new Email($editProfile->newEmail))) {
            throw new \DomainException('Email is already in use.');
        }

        if ($editProfile->isNewEmail()) {
            $newEmail = new Email($editProfile->newEmail);
            $tokenDto = $this->verificationTokenizer->generate();
            $user->changeEmail($newEmail, $tokenDto->token, $tokenDto->expiresAt);
            $this->verificationSender->send($newEmail, $tokenDto->token, $this->emailTemplate);
        }

        $this->em->flush();
    }
}