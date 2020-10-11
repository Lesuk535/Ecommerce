<?php

declare(strict_types=1);

namespace App\Twig\User;

use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class RoleExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('user_role', [$this, 'role'], ['needs_environment' => true , 'is_safe' => ['html']])
        ];
    }

    public function role(Environment $twig, string $role)
    {
        return $twig->render('admin/profile/role.html.twig', ['role' => $role]);
    }
}