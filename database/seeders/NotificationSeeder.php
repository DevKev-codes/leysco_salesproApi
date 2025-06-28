<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Notifications\Notification;

use Carbon\Carbon;

class NotificationSeeder extends Seeder
{
    public function run()
    {
        $notifications = json_decode(file_get_contents(database_path('seeders/data/notifications.json')), true);

        foreach ($notifications as $notification) {
            Notification::create([
                'user_id' => $notification['user_id'],
                'type' => $notification['type'],
                'title' => $notification['title'],
                'message' => $notification['message'],
                'read_at' => $notification['read_at'],
                'created_at' => Carbon::parse($notification['created_at']),
            ]);
        }
    }
}