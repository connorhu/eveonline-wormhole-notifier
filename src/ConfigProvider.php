<?php

namespace EVEOnlineWormhole;

class ConfigProvider
{
    private array $config;

    public function __construct(string $configPath)
    {
        $this->config = json_decode(file_get_contents($configPath), true);
    }

    public function getProjectId(): string
    {
        return $this->config['firebase']['project_id'];
    }

    public function getTopic(): string
    {
        return $this->config['firebase']['topic'];
    }

    public function getServiceAccountConfigJsonPath(): string
    {
        return $this->normalize($this->config['firebase']['service_account_config_json_path']);
    }

    public function getConfigDirectory(): string
    {
        return realpath(__DIR__ . '/../config');
    }

    private function normalize(string $value): string
    {
        return str_replace([
            '%config_dir%',
        ], [
            $this->getConfigDirectory(),
        ], $value);
    }
}
