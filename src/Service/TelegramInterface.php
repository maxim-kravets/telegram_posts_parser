<?php

namespace App\Service;

use danog\MadelineProto\API;

interface TelegramInterface
{
    public function getAPI(): API;
}
