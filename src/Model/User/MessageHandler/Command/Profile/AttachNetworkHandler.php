<?php

declare(strict_types=1);

namespace App\Model\User\MessageHandler\Command\Profile;

use App\Model\User\Domain\Service\IUserRepository;
use App\Model\User\Domain\ValueObject\Id;
use App\Model\User\Message\Command\Profile\AttachNetwork;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class AttachNetworkHandler implements MessageHandlerInterface
{
    private IUserRepository $userRepository;
    private EntityManagerInterface $em;

    public function __construct(IUserRepository $userRepository, EntityManagerInterface $em)
    {
        $this->userRepository = $userRepository;
        $this->em = $em;
    }

    public function __invoke(AttachNetwork $attachFacebook)
    {
        if($this->userRepository->hasByNetworkIdentity($attachFacebook->oauthType, $attachFacebook->identity)) {
            throw new \DomainException('User already exists.');
        }

        $user = $this->userRepository->getById(new Id($attachFacebook->userId));

        $user->attachNetwork(Id::next(), $attachFacebook->oauthType, $attachFacebook->identity);
        $this->em->flush();
    }
}