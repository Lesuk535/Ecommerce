<?php

declare(strict_types=1);

namespace App\Security;

use App\Model\User\Domain\Service\IAvatarUploader;
use App\Model\User\Message\Command\Auth\UserNetworkSignUpRequest;
use App\Model\User\Factory\UserFactory;
use App\Service\Upload\OAuthAvatar;
use App\Service\Upload\UploaderHelper;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Security\Authenticator\SocialAuthenticator;
use League\OAuth2\Client\Provider\FacebookUser;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class FacebookAuthenticator extends SocialAuthenticator
{
    private const OAUTH_TYPE = 'facebook';
    private const AVATAR_NAME = 'facebook';

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
        return $request->attributes->get('_route') === 'connect_facebook_check';
    }

    public function getCredentials(Request $request)
    {
        return $this->fetchAccessToken($this->getFacebookClient());
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        /**
         * @var FacebookUser $facebookUser
         */
        $facebookUser = $this->getFacebookClient()
            ->fetchUserFromToken($credentials);

        $id = $facebookUser->getId();
        $username = self::OAUTH_TYPE . ':' . $id;

        $userNetworkSignUpRequest = new UserNetworkSignUpRequest(
            $facebookUser->getFirstName(),
            $facebookUser->getLastName(),
            self::OAUTH_TYPE,
            $id,
        );

        try {
            return $userProvider->loadUserByUsername($username);
        } catch (UsernameNotFoundException $e) {
            $avatar = new OAuthAvatar($facebookUser->getPictureUrl(), self::AVATAR_NAME);
            $avatarName = $this->avatarUploader->uploadUserAvatar($avatar);

            $userNetworkSignUpRequest->avatar = $avatarName;

            $user = $this->userFactory->createNetworkSignUp($userNetworkSignUpRequest);
            $this->em->persist($user);
            $this->em->flush();
            return $userProvider->loadUserByUsername($username);
        }
    }

    private function getFacebookClient()
    {
        return $this->clientRegistry
            ->getClient('facebook_main');
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return new Response($exception->getMessage(), Response::HTTP_FORBIDDEN);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey)
    {
        $targetUrl = $this->router->generate('app.admin');

        return new RedirectResponse($targetUrl);
    }
}