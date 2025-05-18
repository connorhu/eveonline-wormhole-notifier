<?php

namespace EVEOnlineWormhole;

use Google\Client;

class TokenProvider
{
    private const string ACCESS_TOKEN_FILE = __DIR__.'/../cache/access_token.json';
    private array $tokenData;

    private Client $client;

    public function __construct(
        private readonly ConfigProvider $configProvider
    ) {
        $this->client = new Client();
        $this->client->setAuthConfig(json_decode(file_get_contents($configProvider->getServiceAccountConfigJsonPath()), true));
        $this->client->addScope('https://www.googleapis.com/auth/firebase.messaging');
    }

    public function getAccessToken(): string
    {
        if (!$this->isAccessTokenValid()) {
            $this->tokenData = $this->refreshAccessToken();
            $this->saveAccessToken($this->tokenData);
        }

        return $this->tokenData['access_token'];
    }

    private function readAccessToken(): array
    {
        $data = json_decode(file_get_contents(self::ACCESS_TOKEN_FILE), true);

        if ($data === null) {
            throw new \Exception('Invalid JSON in access token file.');
        }

        return $data;
    }

    private function saveAccessToken(array $data): void
    {
        file_put_contents(self::ACCESS_TOKEN_FILE, json_encode($data, JSON_PRETTY_PRINT));;
    }

    private function isAccessTokenValid(): bool
    {
        if (!is_file(self::ACCESS_TOKEN_FILE)) {
            return false;
        }

        if (!isset($this->tokenData)) {
            $this->tokenData = $this->readAccessToken();
        }

        $expiresAt = $this->tokenData['created'] + $this->tokenData['expires_in'];
        $expiresAt -= 5;
        return $expiresAt < time();
    }

    private function refreshAccessToken(): array
    {
        return $this->client->fetchAccessTokenWithAssertion();
    }
}
