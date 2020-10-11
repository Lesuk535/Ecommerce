<?php

declare(strict_types=1);

namespace App\Controller\Auth;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GoogleController extends AbstractController
{
    /**
     * @Route("/connect/google", name="connect_google_start")
     */
    public function redirectToGoogleConnect(ClientRegistry $clientRegistry): Response
    {
        return $clientRegistry
            ->getClient('google')
            ->redirect(['profile']);
    }

    /**
     * @Route("/google/auth", name="google_auth")
     */
    public function connectGoogleCheck(): Response
    {
        if (!$this->getUser()) {
            return new JsonResponse(['status' => false, 'message' => "User not found!"]);
        } else {
            return $this->redirectToRoute('app.homepage');
        }
    }
}