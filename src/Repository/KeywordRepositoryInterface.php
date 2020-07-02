<?php

namespace App\Repository;

use App\Entity\Keyword;

interface KeywordRepositoryInterface
{
    public function getByName(string $name);

    public function save(Keyword $keyword): void;
}
