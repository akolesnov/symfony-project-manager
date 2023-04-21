<?php

declare(strict_types=1);

namespace App\Controller\Auth\OAuth;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
Use Symfony\Component\Routing\Annotation\Route;

class DiscordController extends AbstractController
{
    #[Route(path: '/oauth/discord', name: 'oauth.discord')]
    public function connect(ClientRegistry $clientRegistry): Response
    {
        return $clientRegistry
            ->getClient('discord')
            ->redirect(['public_profile'], []);
    }

    #[Route(path: '/oauth/discord/check', name: 'oauth.discord_check')]
    public function check(): Response
    {
        return $this->redirectToRoute('home');
    }
}