<?php

namespace App\Controller;

use App\ReadModel\User\UserFetcher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Asset\Context\RequestStackContext;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="app.homepage")
     */
    public function index()
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    /**
     * @Route("/admin", name="app.admin")
     */
    public function admin(UserFetcher $userFetcher)
    {
        $user = $userFetcher->getById($this->getUser()->getId());

        return $this->render('admin/admin.html.twig', [
            'user' => $user
        ]);
    }
}
