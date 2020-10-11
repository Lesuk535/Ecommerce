<?php

declare(strict_types=1);

namespace App\Tests\Unit\User;

use App\Model\User\Domain\Entity\Token;
use App\Model\User\Domain\Entity\User;
use App\Model\User\Domain\ValueObject\Email;
use App\Model\User\Domain\ValueObject\Id;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testSuccessfulSignUpByEmail()
    {
        $user = User::signUpByEmail(
            $id = Id::next(),
            $date = new \DateTimeImmutable(),
            $token = Token::email(Id::next(), new \DateTimeImmutable()),
            $email = new Email('test@mail.com'),
            $passwordHash = 'hash'
        );

        $status = $this->getPrivatePropertyValue($user, 'status');

        self::assertTrue($status->isWait());
        self::assertFalse($status->isActive());

        self::assertEquals($id, $this->getPrivatePropertyValue($user, 'id'));
        self::assertEquals($date, $this->getPrivatePropertyValue($user, 'date'));
        self::assertEquals($token, $this->getPrivatePropertyValue($user, 'tokens')[0]);
        self::assertEquals($email, $this->getPrivatePropertyValue($user, 'email'));
        self::assertEquals($passwordHash, $this->getPrivatePropertyValue($user, 'password'));
    }

    public function testSuccessfulConfirmSignUp()
    {
        $user = $this->createUserViaEmail();

        $user->confirmSignUp($this->getPrivatePropertyValue($user, 'tokens')[0]);

        self::assertFalse($this->getPrivatePropertyValue($user, 'status')->isWait());
        self::assertTrue($this->getPrivatePropertyValue($user, 'status')->isActive());

        self::assertNull($this->getPrivatePropertyValue($user, 'tokens')[0]);
    }

    public function testIfUserIsAlreadyConfirmed()
    {
        $user = $this->createUserViaEmail();

        $token = $this->getPrivatePropertyValue($user, 'tokens')[0];
        $user->confirmSignUp($token);
        $this->expectExceptionMessage('Auth is already confirmed.');
        $user->confirmSignUp($token);
    }

    private function createUserViaEmail()
    {
        return User::signUpByEmail(
            $id = Id::next(),
            $date = new \DateTimeImmutable(),
            $token = Token::email(Id::next(), new \DateTimeImmutable()),
            $email = new Email('test@mail.com'),
            $passwordHash = 'hash'
        );
    }

    private function getPrivatePropertyValue(User $instance, string $property)
    {
        $reflector = new \ReflectionClass($instance);
        $reflectorProperty = $reflector->getProperty($property);
        $reflectorProperty->setAccessible(true);

        return $reflectorProperty->getValue($instance);
    }
}
