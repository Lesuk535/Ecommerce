<?php

declare(strict_types=1);

namespace App\Security;

use App\Model\User\Domain\Service\IAvatarUploader;
use App\Model\User\Message\Command\Auth\UserNetworkSignUpRequest;
use App\Model\User\Factory\UserFactory;
use App\Service\Upload\OAuthAvatar;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Security\Authenticator\SocialAuthenticator;
use League\OAuth2\Client\Provider\GoogleUser;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class GoogleAuthenticator extends SocialAuthenticator
{
    private const OAUTH_TYPE = 'google';
    private const AVATAR_NAME = 'google';

    private $clientRegistry;
    private $em;
    private $router;
    private $userFactory;
    private $avatarUploader;

    public function __construct(
        ClientRegistry $clientRegistry,
        EntityManagerInterface $em,
        RouterInterface $router,
        UserFactory $userFactory,
        IAvatarUploader $avatarUploader
    ) {
        $this->clientRegistry = $clientRegistry;
        $this->em = $em;
        $this->router = $router;
        $this->userFactory = $userFactory;
        $this->avatarUploader = $avatarUploader;
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new RedirectResponse(
            '/connect/',
            Response::HTTP_TEMPORARY_REDIRECT
        );
    }

    public function supports(Request $request)
    {
        return $request->attributes->get('_route') === 'google_auth';
    }

    public function getCredentials(Request $request)
    {
        return $this->fetchAccessToken($this->getGoogleClient());
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        /**
         * @var GoogleUser $googleUser
         */
        $googleUser = $this->getGoogleClient()
            ->fetchUserFromToken($credentials);

        $id = $googleUser->getId();
        $username = self::OAUTH_TYPE . ':' . $id;

        $avatar = new OAuthAvatar($googleUser->getAvatar(), self::AVATAR_NAME);

        $userNetworkSignUpRequest = new UserNetworkSignUpRequest(
            $googleUser->getFirstName(),
            $googleUser->getLastName(),
            self::OAUTH_TYPE,
            $id
        );

        try {
            return $userProvider->loadUserByUsername($username);
        } catch (UsernameNotFoundException $e) {
            $avatarName = $this->avatarUploader->uploadUserAvatar($avatar);
            $userNetworkSignUpRequest->avatar = $avatarName;

            $user = $this->userFactory->createNetworkSignUp($userNetworkSignUpRequest);
            $this->em->persist($user);
            $this->em->flush();
            return $userProvider->loadUserByUsername($username);
        }
    }

    private function getGoogleClient()
    {
        return $this->clientRegistry->getClient('google');
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return new Response($exception->getMessage(), Response::HTTP_FORBIDDEN);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey)
    {
        $targetUrl = $this->router->generate('app.homepage');

        return new RedirectResponse($targetUrl);
    }
}