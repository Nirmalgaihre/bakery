<?php

namespace App\Http\Controllers;

use OneSignal;

class NotificationController extends Controller
{
    public static function sendPush($title, $message)
    {
        OneSignal::sendNotificationToAll(
            $message,
            $url = null,
            $data = ['type' => 'alert'],
            $buttons = null,
            $schedule = null,
            $headings = ['en' => $title]
        );
    }
}