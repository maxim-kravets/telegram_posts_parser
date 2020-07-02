<?php

namespace App\Dto;

class User
{
    private $username;
    private $telegram_id;
    private $firstname;
    private $lastname;

    public function __construct($username, $telegram_id, ?string $firstname, ?string $lastname)
    {
        $this->username = $username;
        $this->telegram_id = $telegram_id;
        $this->firstname = $firstname;
        $this->lastname = $lastname;
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

    public function getLastname(): ?string
    {
        return $this->lastname;
    }
}
