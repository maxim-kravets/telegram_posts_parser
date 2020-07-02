<?php

namespace App\Dto;

class User
{
    private $username;
    private $telegram_id;
    private $firstname;
    private $lastname;
    private $deleted;

    public function __construct($username, $telegram_id, ?string $firstname, ?string $lastname, bool $deleted)
    {
        $this->username = $username;
        $this->telegram_id = $telegram_id;
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->deleted = $deleted;
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

    public function isDeleted(): bool
    {
        return $this->deleted;
    }
}
