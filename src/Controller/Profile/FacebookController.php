<?php

declare(strict_types=1);

namespace App\Controller\Profile;

use App\Model\User\Message\Command\Profile\AttachNetwork;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/profile")
 */
class FacebookController extends AbstractController
{
    /**
     * Link to this controller to start the "connect" process
     *
     * @Route("/connect/facebook_attach", name="connect_facebook_attach_start")
     */
    public function connectAction(ClientRegistry $clientRegistry)
    {
        return $clientRegistry
            ->getClient('facebook_attach')
            ->redirect([
                'public_profile',
            ], []);
    }

    /**
     * After going to Facebook, you're redirected back here
     * because this is the "redirect_route" you configured
     * in config/packages/knpu_oauth2_client.yaml
     *
     * @Route("/connect/facebook_attach/check", name="connect_facebook_attach_check")
     */
    public function connectCheckAction(ClientRegistry $client)
    {
        $client = $client->getClient('facebook_attach');

        $attachFacebook = new AttachNetwork($this->getUser()->getId(), 'facebook', $client->fetchUser()->getId());

        try {
            $this->dispatchMessage($attachFacebook);
            return $this->redirectToRoute('profile');
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
            return $this->redirectToRoute('profile');
        }
    }
}