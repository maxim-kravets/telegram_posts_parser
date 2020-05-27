<?php

namespace App\Repository;

use App\Entity\PostText;

interface PostTextRepositoryInterface
{
    public function getPostText(string $text);

    public function save(PostText $postText): void;
}
