<?php

namespace App\Helpers;

use App\Models\Notification;

class NotificationHelper
{
    public static function notify($userId, $mensaje, $work_id)
    {

        Notification::create([
            'user_id' => $userId,
            'mensaje' => $mensaje,
            'leido' => false,
            'work_id' => $work_id
        ]);
    }

    public static function calendar($userId, $mensaje)
    {

        Notification::create([
            'user_id' => $userId,
            'mensaje' => $mensaje,
            'leido' => false,
        ]);
    }
}
