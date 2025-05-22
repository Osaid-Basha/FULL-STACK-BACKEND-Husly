<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotificationUserSeeder extends Seeder
{
    public function run(): void
    {
        $userIds = DB::table('users')->pluck('id');
        $notificationIds = DB::table('notifications')->pluck('id');

        foreach ($userIds as $user_id) {
            // نربط كل مستخدم بـ 2 إلى 4 إشعارات عشوائية
            $randomNotifications = $notificationIds->random(rand(2, 4));

            foreach ($randomNotifications as $notification_id) {
                DB::table('notification_user')->insert([
                    'user_id' => $user_id,
                    'notification_id' => $notification_id,
                    'is_read' => (bool) rand(0, 1),
                    'read_at' => now(),
                    'status' => 'delivered',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
