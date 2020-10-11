<?php

declare(strict_types=1);

namespace App\Model\User\MessageHandler\Command\User;

use App\Model\User\Domain\Service\IAvatarUploader;
use App\Model\User\Domain\Service\IUserRepository;
use App\Model\User\Domain\ValueObject\Email;
use App\Model\User\Domain\ValueObject\Id;
use App\Model\User\Domain\ValueObject\Name;
use App\Model\User\Domain\ValueObject\Role;
use App\Model\User\Message\Command\User\EditUser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class EditUserHandler implements MessageHandlerInterface
{
    private IAvatarUploader $uploader;
    private IUserRepository $userRepository;
    private EntityManagerInterface $em;

    public function __construct(
        IAvatarUploader $avatarUploader,
        IUserRepository $userRepository,
        EntityManagerInterface $em
    ) {
        $this->uploader = $avatarUploader;
        $this->userRepository = $userRepository;
        $this->em = $em;
    }

    public function __invoke(EditUser $editUser)
    {
        $user = $this->userRepository->getById(new Id($editUser->id));

        if ($editUser->avatar !== null) {
            $uploadedFile = $this->uploader->uploadUserAvatar($editUser->avatar, $editUser->existingFilename);
            $user->changeAvatar($uploadedFile);
        }

        $email = empty($editUser->newEmail)? null : new Email($editUser->newEmail);

        if ($editUser->isNewEmail() && $this->userRepository->hasByEmail($email)) {
            throw new \DomainException('Email is already in use.');
        }

        $user->editUser(new Name($editUser->firstName, $editUser->lastName), $email);

        if ($editUser->role !== $editUser->newRole && $editUser->id !== $editUser->profileId) {
            $user->changeRole(new Role($editUser->newRole));
        }

        $this->em->flush();
    }
}