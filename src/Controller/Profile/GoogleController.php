<?php

declare(strict_types=1);

namespace App\Controller\Profile;

use App\Model\User\Message\Command\Profile\AttachNetwork;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GoogleController extends AbstractController
{
    /**
     * @Route("/connect/google_attach", name="connect_google_attach_start")
     */
    public function redirectToGoogleConnect(ClientRegistry $clientRegistry): Response
    {
        return $clientRegistry
            ->getClient('google_attach')
            ->redirect(['profile']);
    }

    /**
     * @Route("/google/google_attach/check", name="connect_google_attach_check")
     */
    public function connectGoogleCheck(ClientRegistry $client): Response
    {
        $client = $client->getClient('google_attach');

        $attachGoogle = new AttachNetwork($this->getUser()->getId(), 'google', $client->fetchUser()->getId());

        try {
            $this->dispatchMessage($attachGoogle);
            return $this->redirectToRoute('profile');
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
            return $this->redirectToRoute('profile');
        }
    }
}