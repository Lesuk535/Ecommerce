<?php

declare(strict_types=1);

namespace App\Twig\User;

use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AttachOAuthExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('oauth_icon_control', [$this, 'control'], ['needs_environment' => true , 'is_safe' => ['html']])
        ];
    }

    public function control(Environment $twig, ?string $oauthType, ?string $identity, string $href)
    {
        return $twig->render('admin/profile/attach_oauth.html.twig', [
            'oauthType' => $oauthType,
            'identity' => $identity,
            'href' => $href
        ]);
    }
}