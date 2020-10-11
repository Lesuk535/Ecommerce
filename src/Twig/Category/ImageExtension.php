<?php

declare(strict_types=1);

namespace App\Twig\Category;

use Twig\Extension\AbstractExtension;
use Twig\Environment;
use Twig\TwigFunction;

class ImageExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('upload_category_image', [$this, 'upload'], ['needs_environment' => true , 'is_safe' => ['html']])
        ];
    }

    public function upload(Environment $twig, ?string $imagePath, ?string $size = null)
    {
        return $twig->render('admin/categories/upload_image.html.twig', [
            'imagePath' => $imagePath,
            'size' => $size
        ]);
    }
}