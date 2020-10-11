<?php

namespace App\Twig\User;

use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class StatusExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('user_status', [$this, 'status'], ['needs_environment' => true , 'is_safe' => ['html']]),
        ];
    }

    public function status(Environment $twig, string $status)
    {
        return $twig->render('admin/profile/status.html.twig', [
            'status' => $status
        ]);
    }
}
