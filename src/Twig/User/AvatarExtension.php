<?php

declare(strict_types=1);

namespace App\Twig\User;

use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AvatarExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('upload_avatar', [$this, 'upload'], ['needs_environment' => true , 'is_safe' => ['html']])
        ];
    }

    public function upload(Environment $twig, ?string $avatarPath)
    {
        return $twig->render('admin/profile/upload_avatar.html.twig', ['avatarPath' => $avatarPath]);
    }
}