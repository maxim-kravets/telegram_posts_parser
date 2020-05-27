<?php

namespace App\Repository;

use App\Entity\User;

interface UserRepositoryInterface
{
    public function getUserByTelegramId(int $id);

    public function save(User $user): void;
}
