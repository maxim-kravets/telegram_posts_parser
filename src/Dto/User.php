<?php

namespace App\Dto;

class User
{
    private $username;
    private $telegram_id;

    public function __construct($username, $telegram_id)
    {
        $this->username = $username;
        $this->telegram_id = $telegram_id;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getTelegramId()
    {
        return $this->telegram_id;
    }
}
