<?php

namespace App\Dto;

class User
{
    private $username;
    private $telegram_id;

    public function __construct(string $username, int $telegram_id)
    {
        $this->username = $username;
        $this->telegram_id = $telegram_id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getTelegramId(): int
    {
        return $this->telegram_id;
    }
}
