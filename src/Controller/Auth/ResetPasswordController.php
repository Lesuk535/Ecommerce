<?php

declare(strict_types=1);

namespace App\Controller\Auth;

use App\Form\Auth\NewPasswordFormType;
use App\Form\Auth\ResetPasswordFormType;
use App\Model\User\Message\Command\Auth\ResetPasswordConfirm;
use App\Model\User\Message\Command\Auth\ResetPasswordRequest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ResetPasswordController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/reset", name="auth.reset")
     */
    public function reset(Request $request, ResetPasswordRequest $resetPasswordRequest)
    {
        $form = $this->createForm(ResetPasswordFormType::class, $resetPasswordRequest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->dispatchMessage($resetPasswordRequest);
                $this->addFlash('success', 'Confirm you email.');
            } catch (\Exception $e) {
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('auth/reset_password.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/reset_confirm/{token}", name="auth.reset.confirm")
     */
    public function confirm(ResetPasswordConfirm $resetPasswordConfirm, string $token, Request $request)
    {
        $form = $this->createForm(NewPasswordFormType::class, $resetPasswordConfirm);
        $form->handleRequest($request);

        $resetPasswordConfirm->token = $token;

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->dispatchMessage($resetPasswordConfirm);
                $this->addFlash('success', 'Password is successfully changed.');
            } catch (\Exception $e) {
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('auth/confirm_reset_password.html.twig', [
            'form' => $form->createView()
        ]);
    }
}