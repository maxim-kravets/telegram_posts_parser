<?php

namespace App\Repository;

use App\Entity\Post;

interface PostRepositoryInterface
{
    public function getPostByTelegramId(int $id);

    public function save(Post $post): void;
}
