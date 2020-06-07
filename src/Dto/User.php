<?php

namespace App\Dto;

class User
{
    private $username;
    private $telegram_id;
    private $firstname;

    public function __construct($username, $telegram_id, ?string $firstname)
    {
        $this->username = $username;
        $this->telegram_id = $telegram_id;
        $this->firstname = $firstname;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getTelegramId()
    {
        return $this->telegram_id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }
}
