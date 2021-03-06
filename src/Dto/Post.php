<?php

namespace App\Dto;

use App\Entity\Keyword;
use App\Entity\PostText;
use App\Entity\User;

class Post
{
    private $user;
    private $keyword;
    private $telegram_id;
    private $date;
    private $telegram_chat_id;
    private $post_text;
    private $usernames;
    private $chat_name;

    public function __construct(
        User $user,
        Keyword $keyword,
        int $telegram_id,
        \DateTime $date,
        int $telegram_chat_id,
        PostText $post_text,
        array $usernames,
        string $chat_name
    ) {
        $this->user = $user;
        $this->keyword = $keyword;
        $this->telegram_id = $telegram_id;
        $this->date = $date;
        $this->telegram_chat_id = $telegram_chat_id;
        $this->post_text = $post_text;
        $this->usernames = $usernames;
        $this->chat_name = $chat_name;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getKeyword(): Keyword
    {
        return $this->keyword;
    }

    public function getTelegramId(): int
    {
        return $this->telegram_id;
    }

    public function getDate(): \DateTime
    {
        return $this->date;
    }

    public function getTelegramChatId()
    {
        return $this->telegram_chat_id;
    }

    public function getPostText(): PostText
    {
        return $this->post_text;
    }

    public function getUsernames(): array
    {
        return $this->usernames;
    }

    public function getChatName(): string
    {
        return $this->chat_name;
    }
}
