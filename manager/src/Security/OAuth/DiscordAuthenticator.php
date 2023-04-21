<?php

declare(strict_types=1);

namespace App\Security\OAuth;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Model\User\UseCase\Network\Auth\Command;
use App\Model\User\UseCase\Network\Auth\Handler;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\User\UserInterface;
use KnpU\OAuth2ClientBundle\Client\Provider\DiscordClient;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class DiscordAuthenticator extends OAuth2Authenticator
{
    private $urlGenerator;
    private $clients;
    private $handler;

    public function __construct(UrlGeneratorInterface $urlGenerator, ClientRegistry $clients, Handler $handler)
    {
        $this->urlGenerator = $urlGenerator;
        $this->clients = $clients;
        $this->handler = $handler;
    }

    public function supports(Request $request): bool
    {
        return 'oauth.discord_check' === $request->attributes->get('_route');
    }

    public function authenticate(Request $request): Passport
    {
        $accessToken = $this->fetchAccessToken($this->getDiscordClient());

        return new SelfValidatingPassport(
            new UserBadge($accessToken->getToken())
        );
    }

    /**
     * @return DiscordClient|OAuth2Client
     */
    private function getDiscordClient(): DiscordClient
    {
        return $this->clients->getClient('discord');
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
         return new RedirectResponse($this->urlGenerator->generate('home'));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $message = strtr($exception->getMessageKey(), $exception->getMessageData());
        return new Response($message, Response::HTTP_FORBIDDEN);
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new RedirectResponse($this->urlGenerator->generate('app_login'));
    }

    public function getUser($accessToken, UserProviderInterface $userProvider): UserInterface
    {
        $discordUser = $this->getDiscordClient()->fetchUserFromToken($accessToken);

        $network = 'discord';
        $id = $discordUser->getId();
        $username = $network . ':' . $id;

        try {
            return $userProvider->loadUserByIdentifier($username);
        } catch (UserNotFoundException $e) {
            $this->handler->handle(new Command($network, $id));
            return $userProvider->loadUserByIdentifier($username);
        }
    }
}