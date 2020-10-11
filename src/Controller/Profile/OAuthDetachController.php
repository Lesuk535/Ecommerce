<?php

declare(strict_types=1);

namespace App\Controller\Profile;

use App\Model\User\Message\Command\Profile\OAuthDetach;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin")
 */
class OAuthDetachController extends AbstractController
{
    /**
     * @Route("/profile/detach/{oauthType}/{identity}", name="profile_oauth_detach", methods={"DELETE"})
     */
    public function detach(Request $request, string $oauthType, string $identity)
    {
        if (!$this->isCsrfTokenValid('delete', $request->request->get('token'))) {
            return $this->redirectToRoute('profile');
        }

        $oauthDetach = new OAuthDetach($this->getUser()->getId(), $oauthType, $identity);

        try {
            $this->dispatchMessage($oauthDetach);
            return $this->redirectToRoute('profile');
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
            return $this->redirectToRoute('profile');
        }
    }
}