<?php

declare(strict_types=1);

namespace App\Model\User\Factory;

use App\Model\User\Domain\Entity\User;
use App\Model\User\Domain\ValueObject\Id;
use App\Model\User\Domain\ValueObject\Name;
use App\Model\User\DTO\TokenDTO;
use App\Model\User\Message\Command\Auth\UserNetworkSignUpRequest;
use App\Model\User\Message\Command\Auth\UserSignUpRequest;
use App\Model\User\Domain\Service\IHasher;
use App\Model\User\Domain\Service\IUserRepository;
use App\Model\User\Domain\ValueObject\Email;
use App\Model\User\Message\Command\User\CreateUser;

class UserFactory
{
    private IUserRepository $users;
    private IHasher $hasher;

    public function __construct(IUserRepository $users, IHasher $hasher)
    {
        $this->users = $users;
        $this->hasher = $hasher;
    }

    public function createSignUp(
        UserSignUpRequest $request,
        IHasher $hasher,
        TokenDTO $tokenDTO
    ): User {
        $email = new Email($request->email);

        if ($this->users->hasByEmail($email)) {
            throw new \DomainException('User is already exists.');
        }

        return User::signUpByEmail(
            Id::next(),
            new \DateTimeImmutable(),
            new Name($request->firstName, $request->lastName),
            $tokenDTO->token,
            $tokenDTO->expiresAt,
            $email,
            $hasher->hash($request->password)
        );
    }

    public function createNetworkSignUp(
        UserNetworkSignUpRequest $request
    ): User {

        if ($this->users->hasByNetworkIdentity($request->oauthType, $request->identity)) {
            throw new \DomainException('User already exists.');
        }

        return User::signUpByNetwork(
            Id::next(),
            new \DateTimeImmutable(),
            new Name($request->firstName, $request->lastName),
            Id::next(),
            $request->oauthType,
            $request->identity,
            $request->avatar
        );
    }

    public function create(
        CreateUser $request,
        IHasher $hasher
    ){
        $email = new Email($request->email);

        if ($this->users->hasByEmail($email)) {
            throw new \DomainException('User is already exists.');
        }

        return User::create(
            Id::next(),
            new \DateTimeImmutable(),
            new Name($request->firstName, $request->lastName),
            $email,
            $hasher->hash($request->password)
        );
    }
}
