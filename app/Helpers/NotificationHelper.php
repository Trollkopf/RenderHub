<?php

namespace App\Helpers;

use App\Models\Notification;

class NotificationHelper
{
    public static function notify($userId, $mensaje)
    {
        Notification::create([
            'user_id' => $userId,
            'mensaje' => $mensaje,
            'leido' => false,
        ]);
    }
}
