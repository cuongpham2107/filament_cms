<?php

namespace App\Common;
class MessageCommon
{
    public static function createMessage(bool $status, string $message): array
    {
        return [
            'status' => $status,
            'message' => $message,
        ];
    }
}