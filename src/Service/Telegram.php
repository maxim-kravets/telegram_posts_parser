<?php

namespace App\Service;

use danog\MadelineProto\API;

class Telegram implements TelegramInterface
{
    private $madelineProto;

    public function __construct(TelegramConfiguration $telegramConfiguration, string $project_dir)
    {
        $this->madelineProto = new API($project_dir.'/var/sessions/session.madeline', [
            'app_info' => [
                'api_id' => $telegramConfiguration->getApiId(),
                'api_hash' => $telegramConfiguration->getApiHash(),
            ],
            'logger' => [
                'logger' => 0,
            ],
        ]);
    }

    public function getAPI(): API
    {
        return $this->madelineProto;
    }
}
