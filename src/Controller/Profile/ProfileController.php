<?php

declare(strict_types=1);

namespace App\Controller\Profile;

use App\Form\Profile\ProfileFormType;
use App\Model\User\Message\Command\Profile\ChangeEmailConfirm;
use App\Model\User\Message\Command\Profile\EditProfile;
use App\ReadModel\User\UserFetcher;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin")
 */
class ProfileController extends AbstractController
{
    private UserFetcher $userFetcher;

    /**
     * ProfileController constructor.
     * @param $userFeatcher
     */
    public function __construct(UserFetcher $userFetcher)
    {
        $this->userFetcher = $userFetcher;
    }

    /**
     * @Route("/profile", name="profile")
     */
    public function profile(Request $request)
    {
        $user = $this->userFetcher->getById($this->getUser()->getId());

//        $editProfile->id = $this->getUser()->getId();
//        $editProfile->email = $user->email;
//        $editProfile->existingFilename = $user->avatar;

        $editProfile = new EditProfile($this->getUser()->getId(), $user->email, $user->avatar);

        $form = $this->createForm(ProfileFormType::class, $editProfile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->dispatchMessage($editProfile);
                if ($editProfile->isNewEmail()) {
                    $this->addFlash('success', 'Check your email.');
                };
                return $this->redirectToRoute('profile');
            } catch (\Exception $e) {
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('admin/profile/profile.html.twig', [
                'profileFullName' => $user->full_name,
                'user' => $user,
                'form' => $form->createView()
            ]
        );
    }

    /**
     * @Route("/profile/confirm_email/{token}", name="confirm_change_email")
     */
    public function confirmChangeEmail(string $token)
    {
        try {
            $this->dispatchMessage(new ChangeEmailConfirm($token));

        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
            return $this->redirectToRoute('sign.up');
        }

        return $this->redirectToRoute('profile');
    }
}