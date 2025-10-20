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

    public function getConfigurationChecklist(): array
    {
        $checklist = [];

        if ($this->isPlaceholder($this->config['google']['client_id'])) {
            $checklist[] = 'Definir o GOOGLE_CLIENT_ID com o ID do cliente OAuth 2.0 criado no console do Google Cloud.';
        }

        if ($this->isPlaceholder($this->config['google']['client_secret'])) {
            $checklist[] = 'Definir o GOOGLE_CLIENT_SECRET correspondente ao cliente OAuth configurado.';
        }

        if (empty($this->config['google']['redirect_uri']) || !filter_var($this->config['google']['redirect_uri'], FILTER_VALIDATE_URL)) {
            $checklist[] = 'Atualizar a GOOGLE_REDIRECT_URI com a URL pública do endpoint /login.php utilizada na aplicação.';
        }

        if (empty($this->config['google']['hosted_domain']) || $this->isPlaceholder($this->config['google']['hosted_domain'])) {
            $checklist[] = 'Confirmar o domínio restrito (hosted_domain) que poderá autenticar, por exemplo educacao.riopreto.sp.gov.br.';
        }

        if (!extension_loaded('openssl')) {
            $checklist[] = 'Habilitar a extensão OpenSSL no PHP para permitir a comunicação segura com o Google.';
        }

        if (!extension_loaded('curl')) {
            $checklist[] = 'Habilitar a extensão cURL no PHP, requerida pela biblioteca google/apiclient.';
        }

        return $checklist;
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

    private function isPlaceholder(?string $value): bool
    {
        if ($value === null) {
            return true;
        }

        $value = trim($value);

        if ($value === '' || stripos($value, 'SEU_') === 0) {
            return true;
        }

        return in_array($value, ['changeme', 'cliente_id_aqui', 'segredo_aqui'], true);
    }
}
