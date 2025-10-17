<?php

namespace App\Services;

use Google\Client;
use Google\Service\Oauth2;

class GoogleOAuthService
{
    private Client $client;
    private array $config;

    public function __construct()
    {
        $this->config = require __DIR__ . '/../../config/config.php';
        $this->client = new Client();
        $this->client->setClientId($this->config['google']['client_id']);
        $this->client->setClientSecret($this->config['google']['client_secret']);
        $this->client->setRedirectUri($this->config['google']['redirect_uri']);
        $this->client->setAccessType('offline');
        $this->client->setHostedDomain($this->config['google']['hosted_domain']);
        $this->client->setScopes(['email', 'profile']);
    }

    public function getAuthUrl(): string
    {
        return $this->client->createAuthUrl();
    }

    public function authenticate(string $code): array
    {
        $token = $this->client->fetchAccessTokenWithAuthCode($code);
        $this->client->setAccessToken($token);

        $service = new Oauth2($this->client);
        $userInfo = $service->userinfo->get();

        if (strpos($userInfo->email, '@' . $this->config['google']['hosted_domain']) === false) {
            throw new \RuntimeException('Domínio não autorizado.');
        }

        return [
            'google_id' => $userInfo->id,
            'name' => $userInfo->name,
            'email' => $userInfo->email,
        ];
    }
}
