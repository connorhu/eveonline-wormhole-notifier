<?php

namespace EVEOnlineWormhole;

class WormholeIdRepository
{
    private array $knownWormholes;

    public function __construct()
    {
        if (!file_exists(__DIR__.'/../wormhole_ids.json')) {
            file_put_contents(__DIR__.'/../wormhole_ids.json', '{}');
        }

        $this->knownWormholes = json_decode(file_get_contents(__DIR__.'/../wormhole_ids.json'), true);
    }

    public function isKnownId(int $id): bool
    {
        return $this->knownWormholes[$id] ?? false;
    }

    public function saveWormhole(int $id, string $signature): void
    {
        $this->knownWormholes[$id] = $signature;
    }

    public function flush(): void
    {
        file_put_contents(__DIR__.'/../wormhole_ids.json', json_encode($this->knownWormholes, JSON_PRETTY_PRINT));
    }
}
