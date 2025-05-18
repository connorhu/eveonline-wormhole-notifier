<?php

namespace EVEOnlineWormhole;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class FCMNotificationClient
{
    private TokenProvider $tokenProvider;
    private HttpClientInterface $httpClient;

    public function __construct(private readonly ConfigProvider $configProvider) {
        $this->httpClient = HttpClient::create();
        $this->tokenProvider = new TokenProvider($configProvider);
    }

    public function sendNotification(array $ids): void
    {
        $message = [
            'message' => [
                'topic' => $this->configProvider->getTopic(),
                'data' => [
                    'new_ids' => implode(',', $ids)
                ],
                'android' => [
                    'priority' => 'HIGH',
                ],
            ]
        ];

        // HTTP kérés
        $url = 'https://fcm.googleapis.com/v1/projects/'.$this->configProvider->getProjectId().'/messages:send';

        $this->httpClient->request('POST', $url, [
            'headers' => [
                'Authorization: Bearer '.$this->tokenProvider->getAccessToken(),
            ],
            'json' => $message,
        ]);
    }
}
