<?php

declare(strict_types=1);

namespace App\Service\Auth;

use App\Model\User\Domain\Entity\Token;
use App\Model\User\Domain\Service\TokenSender;
use App\Model\User\Domain\ValueObject\Email;
use App\Model\User\Domain\ValueObject\Id;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

final class VerificationTokenSender implements TokenSender
{
    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function send(Email $email, Id $token, string $templatePath): void
    {
        $email = (new TemplatedEmail())->from('manager@mail.com')
            ->to($email->getValue())
            ->htmlTemplate($templatePath)
            ->context(['token' => $token]);
        // TODO catch exception
        $this->mailer->send($email);
    }

}