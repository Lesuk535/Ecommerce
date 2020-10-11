<?php

declare(strict_types=1);

namespace App\Model\User\MessageHandler\Command\User;

use App\Model\User\Domain\Service\IAvatarUploader;
use App\Model\User\Domain\ValueObject\Email;
use App\Model\User\Message\Command\User\UploadUserAvatar;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class UploadUserAvatarHandler implements MessageHandlerInterface
{
    private IAvatarUploader $uploader;
    private UserRepository $userRepository;
    private EntityManagerInterface $em;

    public function __construct(IAvatarUploader $uploader, UserRepository $userRepository, EntityManagerInterface $em)
    {
        $this->uploader = $uploader;
        $this->userRepository = $userRepository;
        $this->em = $em;
    }

    public function __invoke(UploadUserAvatar $uploadUserAvatar)
    {
        $email = new Email($uploadUserAvatar->email);
        $user = $this->userRepository->getByEmail($email);
        $uploadedFile = $this->uploader->uploadUserAvatar($uploadUserAvatar->uploadedFile, $uploadUserAvatar->existingFilename);
        $user->changeAvatar($uploadedFile);
        $this->em->flush();
    }
}