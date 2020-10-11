<?php

declare(strict_types=1);

namespace App\Model\User\MessageHandler\Command\Profile;

use App\Model\User\Domain\ValueObject\Id;
use App\Model\User\Message\Command\Profile\OAuthDetach;
use App\Repository\UserRepository;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class OAuthDetachHandler  implements MessageHandlerInterface
{
    private UserRepository$userRepository;
    private EntityManagerInterface $em;

    public function __construct(UserRepository $userRepository, EntityManagerInterface $em)
    {
        $this->userRepository = $userRepository;
        $this->em = $em;
    }

    public function __invoke(OAuthDetach $oauthDetach)
    {
        $user = $this->userRepository->getById(new Id($oauthDetach->userId));

        $user->detachNetwork($oauthDetach->oauthType, $oauthDetach->identity);
        $this->em->flush();
    }
}