<?php

namespace App\Service;

class TelegramConfiguration
{
    private $api_id;
    private $api_hash;

    public function __construct(int $api_id, string $api_hash)
    {
        $this->api_id = $api_id;
        $this->api_hash = $api_hash;
    }

    public function getApiId(): int
    {
        return $this->api_id;
    }

    public function getApiHash(): string
    {
        return $this->api_hash;
    }
}
