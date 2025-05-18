<?php

namespace EVEOnlineWormhole;

use Symfony\Component\HttpClient\HttpClient;

class EVEScoutClient
{
    public function fetchSignatures(): array
    {
        $endpoint = 'https://api.eve-scout.com/v2/public/signatures';
        $client = HttpClient::create();
        $response = $client->request('GET', $endpoint);

        return $response->toArray();
    }
}
