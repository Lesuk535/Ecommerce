<?php

namespace App\Controller\Auth;

use App\Form\Auth\SignUpFormType;
use App\Model\User\Message\Command\Auth\ConfirmingUserByToken;
use App\Model\User\Message\Command\Auth\UserSignUpRequest;
use App\ReadModel\User\UserFetcher;
use App\Security\LoginFormAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

class RegisterController extends AbstractController
{
    private $em;
    private $userFetcher;

    public function __construct(EntityManagerInterface $entityManager, UserFetcher $userFetcher)
    {
        $this->em = $entityManager;
        $this->userFetcher = $userFetcher;
    }

    /**
     * @Route("/signup", name="sign.up")
     */
    public function signUp(
        Request $request,
        UserSignUpRequest $signUpRequest
    ) {
        $form = $this->createForm(SignUpFormType::class, $signUpRequest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->dispatchMessage($signUpRequest);
                $this->addFlash('success', 'Check your email.');
                return $this->redirectToRoute('sign.up');
            } catch (\Exception $e) {
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('auth/signup.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/confirm/{token}", name="confirm.email")
     */
    public function confirmEmail(
        string $token,
        UserProviderInterface $userProvider,
        GuardAuthenticatorHandler $guardHandler,
        LoginFormAuthenticator $authenticator,
        Request $request
    ) {

        if (!$user = $this->userFetcher->findByVerificationToken($token)) {
            throw new \DomainException('Incorrect or already confirmed token.');
        }

        try {
            $this->dispatchMessage(new ConfirmingUserByToken($token));
            return $guardHandler->authenticateUserAndHandleSuccess(
                $userProvider->loadUserByUsername($user->email),
                $request,
                $authenticator,
                'main'
            );
//            $this->addFlash('success','Registration successfully completed!');
        } catch (\Exception $e ) {
            $this->addFlash('error', $e->getMessage());
            return $this->redirectToRoute('sign.up');
        }

    }
}
